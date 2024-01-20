<?php

namespace Modules\Orders\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Files\Models\TmpFile;
use Modules\History\Models\HistoryUpload;
use Illuminate\Http\Request;
use Modules\Orders\Jobs\ParseExelFile;
use Modules\Orders\Jobs\ParseExelFileSpout;
use OpenSpout\Reader\XLSX\Reader;
use PhpOffice\PhpSpreadsheet\IOFactory;

class OrderUploadService
{
    public int $season_id = 0;
    public int $file_id;
    public int $count_rows = 0;
    public string $file_type = 'Xlsx';
    public int $column_count;
    public string $path;
    public array $rows = [];
    public Request $request;
    //Минимальное кол-во колонок в эксель таблице = 12 (Трек номер)
    const DEFAULT_COLUMN_COUNT = 12;
    const VALIDATE_COUNT_ROWS = 10;
    const VALIDATE_CHUNK_SIZE = 30;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->column_count = self::DEFAULT_COLUMN_COUNT;
        $this->getRequestFields();
    }

    //Сборка полей пришедших из формы
    public function getRequestFields(): void
    {
        $this->season_id = $this->request->post('season_id')['value'];
        $this->path = $this->request->post('file')['items'][0]['response']['path'];
        $this->file_id = $this->request->post('file')['items'][0]['response']['id'];
    }

    public function getArray(): array
    {
        return [
            'season' => $this->season_id,
            'path'   => $this->path,
            'rows'   => $this->rows,
        ];
    }

    public function save(): array
    {
        $validate = $this->validateFileNew();
        if ($validate['status']) {
            try {
                $res = (new HistoryUpload())->create($this->getFieldsHistory());
                ParseExelFileSpout::dispatch($res)->onConnection('redis-long-running');
                //Обновим статус файла выгрузки
                (new TmpFile())->find($this->file_id)->update(['status' => 1]);
                //Добавим запись в таблицу "Историй выгрузок"
                return ['status' => true, 'id' => $res->id];
            } catch (\Exception $exception) {
                Log::debug('errors:' . $exception->getMessage());
                return ['status' => false, 'errors' => $exception->getMessage()];
            }
        } else {
            return ['status' => false, 'errors' => 'Данный файл имеет некорректный вид, выгрузка объектов невозможна! Образец корректного файла доступен по ссылке ниже.', 'validateError' => true];
        }
    }

    public function getFieldsHistory(): array
    {
        return [
            'user_id'   => Auth::user()->id,
            'file_id'   => $this->file_id,
            'season_id' => $this->season_id,
        ];
    }

    public function getCountRows(): int
    {
        $file_exel = Storage::disk('public')->path($this->path);
        $objReader = IOFactory::createReader($this->file_type);
        $objReader->setReadDataOnly(true);
        $objReader->setReadEmptyCells(false);
        $objPHPExcel = $objReader->load($file_exel);
        //Кол-во строк в файле
        $count_rows = $objPHPExcel->getActiveSheet()->getHighestRow();
        unset($objReader);
        unset($objPHPExcel);
        return $count_rows;
    }

    public function validateFile(): array
    {
        $errors = [];
        $status = true;
        $file_exel = Storage::disk('public')->path($this->path);
        $this->file_type = IOFactory::identify($file_exel);
        if ($this->file_type !== 'Xlsx') {
            $errors[] = ['value' => 'Некорректный формат файла', 'critical' => true];
        }
        $objReader = IOFactory::createReader($this->file_type);
        $objReader->setReadDataOnly(true);
        $objReader->setReadEmptyCells(false);
        for ($startRow = 2; $startRow <= self::VALIDATE_COUNT_ROWS; $startRow += self::VALIDATE_CHUNK_SIZE) {
            $chunkFilter = new ChunkService($startRow, self::VALIDATE_CHUNK_SIZE);
            $objReader->setReadFilter($chunkFilter);
            $objPHPExcel = $objReader->load($file_exel);
            //Получаем массив строк для проверки
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(false, false, false, true);
            unset($objReader);
            unset($objPHPExcel);
            $correct_header = config('orders.columnName');
            foreach ($sheetData as $key => $items) {
                if ((int) $key === 1) {
                    //Проверяем файл на корректность названия колонок
                    $iterator = 0;
                    foreach ($items as $column_name => $item) {
                        if ($iterator < 12) {
                            if (array_key_exists($column_name, $correct_header) && strtolower($correct_header[$column_name]) !== strtolower($item)) {
                                $errors[] = [
                                    'value'    => 'Неверный порядок колонок, или их название',
                                    'critical' => true,
                                ];
                            }
                        }
                        $iterator++;
                    }
                } else {
                    $flag = false;
                    $local_error = false;
                    foreach ($items as $index => $item) {
                        //Проверим что текущая строка с данными
                        if ($index === 'A' && $item) {
                            $flag = true;
                            if (strlen($item) !== 7) {
                                $errors[] = [
                                    'value'    => 'Некорректная длина кода, строка:' . (int) $key,
                                    'critical' => false,
                                ];
                            }
                        }
                        if ($flag && ($index === 'B' || $index === 'C')) {
                            if (! $item) {
                                $local_error = true;
                            }
                            if ($index === 'C' && ! is_numeric($item)) {
                                $local_error = true;
                            }
                        }
                    }
                    if ($local_error) {
                        $errors[] = [
                            'value'    => 'Ошибка в строке - ' . (int) $key,
                            'critical' => false,
                        ];
                    }
                }
            }
            $found_key = array_search(true, array_column($errors, 'critical'));
            if ($found_key !== false || count($errors) > 10) {
                $status = false;
            }
        }
        return ['errors' => $errors, 'status' => $status];
    }

    public function validateFileNew(): array
    {
        $errors = [];
        $status = true;
        $file_exel = Storage::disk('public')->path($this->path);
        $this->file_type = IOFactory::identify($file_exel);
        if ($this->file_type !== 'Xlsx') {
            $errors[] = ['value' => 'Некорректный формат файла', 'critical' => true];
        }
        $reader = new Reader();
        $reader->open($file_exel);
        $correct_header = config('orders.columnName');
        foreach ($reader->getSheetIterator() as $list => $sheet) {
            if ($list == 1) {
                foreach ($sheet->getRowIterator() as $key => $current_row) {
                    if ($key < self::VALIDATE_CHUNK_SIZE) {
                        $current_row = $current_row->getCells();
                        if ($key === 1) {
                            //Проверяем файл на корректность названия колонок
                            $iterator = 0;
                            foreach ($current_row as $column_name => $item) {
                                $item = $item->getValue();
                                if ($iterator < 12) {
                                    if (array_key_exists((int) $column_name, $correct_header) && strtolower($correct_header[(int) $column_name]) !== strtolower($item)) {
                                        $errors[] = [
                                            'value'    => 'Неверный порядок колонок, или их название',
                                            'critical' => true,
                                        ];
                                    }
                                } else {
                                    break;
                                }
                                $iterator++;
                            }
                        } else {
                            $flag = false;
                            $local_error = false;
                            foreach ($current_row as $index => $item) {
                                //Проверим что текущая строка с данными
                                $item = $item->getValue();
                                if ($index === 0 && $item) {
                                    $flag = true;
                                    if (strlen($item) !== 7) {
                                        $errors[] = [
                                            'value'    => 'Некорректная длина кода, строка:' . $key,
                                            'critical' => false,
                                        ];
                                    }
                                }
                                if ($flag && ($index === 1 || $index === 2)) {
                                    if (! $item) {
                                        $local_error = true;
                                    }
                                    if ($index === 2 && ! is_numeric($item)) {
                                        $local_error = true;
                                    }
                                }
                            }
                            if ($local_error) {
                                $errors[] = [
                                    'value'    => 'Ошибка в строке - ' . $key,
                                    'critical' => false,
                                ];
                            }
                        }
                    } else {
                        break;
                    }
                }
            }
        }
        $found_key = array_search(true, array_column($errors, 'critical'));
        if ($found_key !== false || count($errors) > 10) {
            $status = false;
        }
        return ['errors' => $errors, 'status' => $status];
    }
}
