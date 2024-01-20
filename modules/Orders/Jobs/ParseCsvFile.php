<?php

declare(strict_types=1);

namespace Modules\Orders\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Orders\Resources\RowResource;
use Modules\Orders\Services\OrderService;


class ParseCsvFile implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected string $path;
    protected int $column_count;
    protected int $season_id;
    protected int $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $user_id, string $path, int $column_count, int $season_id)
    {
        $this->user_id = $user_id;
        $this->path = $path;
        $this->column_count = $column_count;
        $this->season_id = $season_id;
    }

    /**
     * Execute the job.
     *
     * @param OrderService $service
     * @return void
     */
    public function handle(OrderService $service)
    {
        $fh = fopen($this->path, "r");
        // делаем пропуск первой строки, смещая указатель на одну строку
        fgetcsv($fh, 0, ',');
        //читаем построчно содержимое CSV-файла
        $iter = 0;
        while (($c_row = fgetcsv($fh, 0, ',')) !== false) {
            if ($iter !== 0) {
                $row = [];
                for ($i = 0; $i < $this->column_count; $i++) {
                    $row[] = $c_row[$i];
                }
                //Проверка что у строки заполнен код заказа, также смотрим длину строки (есть строки для братьев) их пропускаем.
                if ($row[0] && strlen((string) $row[0]) <= 8) {
                    $row[] = $this->season_id;
                    $service->createOrUpdate((new RowResource($row))->toArray(), $this->user_id);
                }
            }
            $iter++;
        }
    }
}
