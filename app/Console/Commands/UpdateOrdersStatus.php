<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use LapayGroup\RussianPost\Exceptions\TrackingException;
use LapayGroup\RussianPost\Providers\Tracking;
use Modules\ApiTask\Models\ApiTask;
use Modules\Orders\Models\Orders;
use Modules\OrderTracking\Services\RussianPost;
use SoapFault;

class UpdateOrdersStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление статусов заказов по трек номерам из почты России';
    /**
     * Execute the console command.
     *
     * @return void
     * @throws SoapFault
     */
    public function handle(): void
    {
        ini_set('memory_limit', '512M');
        $tasks = ApiTask::query()->active()->get();
        foreach ($tasks as $value) {
            $russianPost = new RussianPost();
            try {
                $this->info('Получим трэкномера по предыдущиму билету = ' . $value->api_id);
                $trackingList = $russianPost->getTrackingTicketsStatuses($value->api_id);
                if ($trackingList) {
                    $russianPost->updateOrdersStatus($trackingList);
                }
                $value->status = true;
                $value->save();
            } catch (\Exception $e) {
                Log::debug(date('d.m.Y H:i:s') . "\n" . print_r('cronRussianPost exception: ' . $e->getMessage(), true) . "\n\n");
            }
        }
    }
}
