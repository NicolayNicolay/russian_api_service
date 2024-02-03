<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use LapayGroup\RussianPost\Providers\Tracking;
use Modules\Orders\Models\Orders;
use SoapFault;

class GetPaymentStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получение статусов оплаты';

    public function start(): void
    {
        $numbers = (new Orders())->activeSeason()->where('track', '34498585349411')->pluck('track')->toArray();
        $Tracking = new Tracking('single', config('tracking.account'));
        foreach ($numbers as $number) {
            try {
                $res = $Tracking->getNpayInfo($number);
                dd($res);
                $order = (new Orders())->where('track', $number)->first();
                if ($res) {
                    $item = $res[count($res) - 1];
                    $order->payment_state = $item->EventType ?? null;
                    $order->payment_sum = $item->SumPaymentForward ?? null;
                    $order->payment_date = $item->EventDateTime ? Carbon::createFromFormat('Y-m-d\TH:i:s.uP', $item->EventDateTime)->format('Y-m-d H:i:s') : null;
                    $order->payment_place_index = $item->IndexEvent ?? null;
                    if ($item->EventType == 3) {
                        $order->processed_paid = true;
                    }
                } else {
                    $order->processed_paid = true;
                }
                $order->save();
            } catch (\Exception $e) {
                Log::debug(date('d.m.Y H:i:s') . "\n" . print_r('Ошибка при получение статуса оплаты у объекта по трек номеру - : ' . $number . '. Ошибка:' . $e->getMessage(), true) . "\n\n");
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws SoapFault
     */
    public function handle(): void
    {
        $this->info('Делаем выборку объектов');
        $numbers = (new Orders())->final()->payd()->processed()->activeSeason()->get();
        $bar = $this->getOutput()->createProgressBar(count($numbers));
        $Tracking = new Tracking('single', config('tracking.account'));
        foreach ($numbers as $number) {
            sleep(1);
            try {
                $res = $Tracking->getNpayInfo($number->track);
                if ($res) {
                    $item = $res[count($res) - 1];
                    $number->payment_state = $item->EventType ?? null;
                    $number->payment_sum = $item->SumPaymentForward ? $item->SumPaymentForward / 100 : null;
                    $number->payment_date = $item->EventDateTime ? Carbon::createFromFormat('Y-m-d\TH:i:s.uP', $item->EventDateTime)->format('Y-m-d H:i:s') : null;
                    $number->payment_place_index = $item->IndexEvent ?? null;
                    if ($item->EventType == 3) {
                        $number->processed_paid = true;
                    }
                } else {
                    $number->processed_paid = true;
                }
                $number->save();
            } catch (\Exception $e) {
                Log::debug(date('d.m.Y H:i:s') . "\n" . print_r('Ошибка при получение статуса оплаты у объекта по трек номеру - : ' . $number . '. Ошибка:' . $e->getMessage(), true) . "\n\n");
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info('Выполнено');
    }
}
