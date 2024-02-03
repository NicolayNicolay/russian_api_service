<?php

declare(strict_types=1);

namespace Modules\Sms\Jobs;

use igorbunov\Smsc\Config\Config;
use igorbunov\Smsc\SmscJsonApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Sms\Models\Sms;


class SendSms implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public Sms $element;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Sms $element)
    {
        $this->element = $element;
    }

    public function handle(): void
    {
        $api = new SmscJsonApi(new Config(config('app.sms_login'), config('app.sms_password')));
        $text = $this->element->text;
        $replace_makro = config('sms.makro_replace');
        foreach ($this->element->orders as $order) {
            $tmp_text = $text;
            foreach ($replace_makro as $key => $item) {
                if (strripos($tmp_text, $key)) {
                    if ($order[$item]) {
                        $tmp_text = str_replace($key, $order[$item], $tmp_text);
                    } else {
                        $tmp_text = str_replace($key, '', $tmp_text);
                    }
                }
            }
            $api->sendSms($order->phone_relatives, $tmp_text);
        }
    }
}
