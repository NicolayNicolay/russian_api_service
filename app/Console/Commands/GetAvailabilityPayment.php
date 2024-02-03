<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use LapayGroup\RussianPost\Providers\Tracking;
use Modules\Orders\Models\Orders;
use SoapFault;

class GetAvailabilityPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:avail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получение статусов наличия наложенного платежа';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws SoapFault
     */
    public function handle()
    {
        $this->info('Делаем выборку объектов');
        $query = Orders::query()->track()->activeSeason()->whereNull('status_availability_payment');
        $bar = $this->getOutput()->createProgressBar($query->count());
        $Tracking = new Tracking('single', config('tracking.account'));
        $query->chunkById(2000, function ($numbers) use (&$bar, $Tracking) {
            foreach ($numbers as $number) {
                sleep(1);
                try {
                    $res = $Tracking->getOperationsByRpo($number->track);
                    if ($res) {
                        $item = $res[0];
                        if ($item->FinanceParameters && isset($item->FinanceParameters->Payment)) {
                            $number->availability_payment = true;
                        }
                        $number->status_availability_payment = true;
                        $number->availability_payment_date = Carbon::now()->format('Y-m-d H:i:s');
                        $number->save();
                    }
                } catch (\Exception $e) {
                    Log::debug(date('d.m.Y H:i:s') . "\n" . print_r('Ошибка при получение наличия оплаты у объекта по трек номеру - : ' . $number->track . '. Ошибка:' . $e->getMessage(), true) . "\n\n");
                }
                $bar->advance();
            }
        });
        $bar->finish();
        $this->info('Выполнено');
    }
}
