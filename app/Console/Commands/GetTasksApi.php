<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Orders\Models\Orders;
use Modules\OrderTracking\Services\RussianPost;
use SoapFault;

class GetTasksApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление статусов заказов по трек номерам из почты России';


    public function start(): void
    {
//        $numbers = (new Orders())->track()->activeSeason()->where('track', '34498585456447')->pluck('track')->toArray();
//        try {
//            $trackingList = RussianPost::getTrackingTicketsStatuses();
//            if ($trackingList) {
//                RussianPost::updateOrdersStatus($trackingList);
//            }
//        } catch (\Exception $e) {
//            Log::debug(date('d.m.Y H:i:s') . "\n" . print_r('cronRussianPost exception: ' . $e->getMessage(), true) . "\n\n");
//        }
//        RussianPost::getTrackingTickets($numbers);
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws SoapFault
     */
    public function handle(): void
    {
        ini_set('memory_limit', '512M');
        $chunk_size = 500;
        $count = 0;
        Orders::query()->track()->activeSeason()->orderBy('id')->chunk($chunk_size, function ($numbers) use (&$count, $chunk_size) {
            $count += $chunk_size;
            $this->info('Обработали ' . $count . ' элементов');
            $russianPost = new RussianPost();
            $res = $numbers->pluck('track')->toArray();
            $final_res = [];
            foreach ($res as $item) {
                if (preg_match('#^\d+$#is', $item)) {
                    $final_res[] = $item;
                }
            }
            $this->info('Создадим новые билеты');
            $russianPost->getTrackingTickets($final_res);
        });
    }
}
