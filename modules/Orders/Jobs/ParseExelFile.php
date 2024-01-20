<?php

declare(strict_types=1);

namespace Modules\Orders\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\History\Models\HistoryUpload;
use Modules\Orders\Resources\RowResource;
use Modules\Orders\Services\OrderService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;


class ParseExelFile implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected HistoryUpload $history;
    const DEFAULT_COLUMN_COUNT = 12;
    const CHUNK_SIZE = 300;
    const NORMAL_LENGTH = 8;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(HistoryUpload $history)
    {
        $this->history = $history;
    }

    public function uniqueId(): int
    {
        return $this->history->id;
    }

    /**
     * Execute the job.
     *
     * @param OrderService $service
     * @return void
     * @throws Exception
     */
    public function handle(OrderService $service)
    {
        ini_set('memory_limit', '512M');
        Log::debug('start:' . Carbon::now()->format('Y-m-d H:i:s'));
        try {
            if ($this->history->processed < $this->history->count_string) {
                $file_exel = Storage::disk('public')->path($this->history->file->path);
                $objReader = IOFactory::createReader("Xlsx");
                $objReader->setReadDataOnly(true);
                $objReader->setReadEmptyCells(false);
                $objPHPExcel = $objReader->load($file_exel);
                $array = $objPHPExcel->getActiveSheet()->toArray();
                unset($objReader);
                $start_index = $this->history->processed != 0 ? ceil($this->history->processed / self::CHUNK_SIZE) : 0;
                $rows = [];
                $processed = $this->history->processed;
                for ($i = $start_index; $i < count($array); $i++) {
                    if ($i === 0) {
                        continue;
                    }
                    $current_index = $i;
                    $row = [];
                    for ($j = 0; $j < self::DEFAULT_COLUMN_COUNT; $j++) {
                        $row[] = $array[$i][$j];
                    }
                    //Проверка что у строки заполнен код заказа, также смотрим длину строки (есть строки для братьев) их пропускаем.
                    if ($row[0] && strlen((string) $row[0]) <= self::NORMAL_LENGTH) {
                        array_push($row, $this->history->season_id, $this->history->user_id);
                        $rows[] = (new RowResource($row))->toArray();
                    }
                    $difference = $current_index - $start_index;
                    if ($difference > self::CHUNK_SIZE) {
                        $service->createsOrUpdates($rows);
                        $rows = [];
                        $processed += $difference;
                        $start_index = $current_index;
                        Log::debug('difference, записали:' . $current_index . ' в -' . Carbon::now()->format('Y-m-d H:i:s'));
                        $this->history->update(['processed' => $processed]);
                    }
                }
                $service->createsOrUpdates($rows);
                $processed = count($array);
                $this->history->update(['processed' => $processed, 'status' => 1]);
            } else {
                Log::debug('Данный файл полностью выгружен: ' . $this->history->file->id);
            }
        } catch (\Exception $exception) {
            Log::debug('Error:' . $exception->getMessage());
        }
        Log::debug('finish:' . Carbon::now()->format('Y-m-d H:i:s'));
    }
}
