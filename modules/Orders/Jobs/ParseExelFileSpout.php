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
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Options;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

class ParseExelFileSpout implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected HistoryUpload $history;
    const DEFAULT_COLUMN_COUNT = 12;
    const CHUNK_SIZE = 300;
    const NORMAL_LENGTH = 8;

    const MIN_NORMAL_LENGTH = 6;

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
            $file_exel = Storage::disk('public')->path($this->history->file->path);
            $reader = new Reader();
            $reader->open($file_exel);
            $start_index = $this->history->processed;
            $processed = $this->history->processed;
            $rows = [];
            foreach ($reader->getSheetIterator() as $list_index => $sheet) {
                if ($list_index > 1) {
                    break;
                }
                $count = 0;
                foreach ($sheet->getRowIterator() as $key => $current_row) {
                    if ($key === 1) {
                        continue;
                    }
                    $iterator = $key - 1;
                    if ($iterator > $start_index) {
                        $row = [];
                        $rowAsArray = $current_row->toArray();
                        for ($j = 0; $j < self::DEFAULT_COLUMN_COUNT; $j++) {
                            $row[] = $rowAsArray[$j] ?? '';
                        }
                        //Проверка что у строки заполнен код заказа, также смотрим длину строки (есть строки для братьев) их пропускаем.
                        $code = preg_replace("/\s+/", "", (string) $row[0]);
                        if ($code && strlen((string) $code) > self::MIN_NORMAL_LENGTH && strlen((string) $code) <= self::NORMAL_LENGTH) {
                            array_push($row, $this->history->season_id, $this->history->user_id);
                            $rows[] = (new RowResource($row))->toArray();
                        }
                        $difference = $iterator - $start_index;
                        if ($difference > self::CHUNK_SIZE) {
                            $service->createsOrUpdates($rows);
                            $rows = [];
                            $processed += $difference;
                            $start_index = $iterator;
                            Log::debug('difference, записали:' . $iterator . ' в -' . Carbon::now()->format('Y-m-d H:i:s'));
                            $this->history->update(['processed' => $processed]);
                        }
                    }
                    $count = $iterator;
                }
                $service->createsOrUpdates($rows);
                $processed = $count + 1;
                $this->history->update(['processed' => $processed, 'status' => 1]);
                //Обрабатываем только 1 страницу
            }
            $reader->close();
        } catch (\Exception $exception) {
            Log::debug('Error:' . $exception->getMessage());
        }
        Log::debug('finish:' . Carbon::now()->format('Y-m-d H:i:s'));
    }
}
