<?php

namespace Modules\OrderTracking\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use LapayGroup\RussianPost\Entity\Order;
use LapayGroup\RussianPost\Exceptions\TrackingException;
use Modules\ApiTask\Models\ApiTask;
use Modules\Orders\Models\Orders;
use Modules\OrdersErrors\Models\OrdersErrors;
use SoapFault;
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\Tracking;

class RussianPost
{
    const UPLOAD_TICKETS_FILE = 'tracking/ticket.json';
    const DELIVERY_RUSSIAN_POST = 29;

    static function getConfig()
    {
        return config('tracking.account');
    }

    static function convertString($str): array | bool | string | null
    {
        return mb_convert_encoding($str, 'windows-1251', 'utf-8');
    }

    /**
     * @throws SoapFault
     */
    static function getTrackingStatus($number)
    {
        $Tracking = new Tracking('single', self::getConfig());
        $statuses = $Tracking->getOperationsByRpo($number);

        $result = false;

        if (! empty($statuses)) {
            foreach ($statuses as $item) {
                $result = [
                    'number'      => $number,
                    'code'        => $item->OperationParameters->OperType->Id,
                    'description' => self::convertString($item->OperationParameters->OperType->Name),
                    'date'        => date('d.m.Y H:i:s', strtotime($item->OperationParameters->OperDate)),
                ];
            }
        }

        return $result;
    }

    /**
     * @throws SoapFault
     */
    static function getTrackingTickets($numbers): void
    {
        $Tracking = new Tracking('pack', self::getConfig());
        $result = $Tracking->getTickets($numbers);
        $tasks = new ApiTask();
        foreach ($result['tickets'] as $ticket) {
            $tasks->create([
                               'api_id' => $ticket,
                           ]);
        }
    }

    /**
     * @throws SoapFault
     * @throws TrackingException
     */
    static function getTrackingTicketsStatuses($ticket): array
    {
        $result = [];
        $Tracking = new Tracking('pack', self::getConfig());
        $statuses = $Tracking->getOperationsByTicket($ticket);
        foreach ($statuses as $number => $items) {
            if (! empty($items)) {
                if (! is_array($items) && $items->Error) {
                    $order = (new Orders())->where('track', $items->Barcode)->first();
                    $old_error = $order->error();
                    $error = (new OrdersErrors())->create([
                                                              'text' => $items->Error->ErrorName,
                                                          ]);
                    $order->error_id = $error->id;
                    $order->save();
                    $old_error?->delete();
                    Log::debug(date('d.m.Y H:i:s') . "\n" . print_r('По данному заказу: ' . $items->Barcode . ', пришла ошибка: ' . $items->Error->ErrorName, true) . "\n\n");
                } else {
                    $item = $items[count($items) - 1];
                    $result[] = [
                        'number'      => $number,
                        'code'        => $item->OperTypeID . '_' . $item->OperCtgID . '_' . (int) $item->isFinal,
                        'description' => self::convertString($item->OperName) . ' (' . self::convertString($item->OperCtgName) . ')',
                        'date'        => $item->DateOper ?? '',
                        'final'       => $item->isFinal,
                    ];
                }
            }
        }

        return $result;
    }

    static function updateOrdersStatus($trackingList)
    {
        foreach ($trackingList as $tracking) {
            $order = (new Orders())->where('track', $tracking['number'])->first();
            if ($tracking['date']) {
                $order->date_operation = Carbon::parse($tracking['date'])->format('Y-m-d H:i:s');
            }
            if (($order->status && $order->status != $tracking['code']) || $order->status == null) {
                $order->last_status_updated = Carbon::now();
            }
            $order->status = $tracking['code'];
            $order->final = $tracking['final'] ?? null;
            $order->save();
        }
    }
}
