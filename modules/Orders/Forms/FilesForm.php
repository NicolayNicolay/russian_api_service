<?php

declare(strict_types=1);

namespace Modules\Orders\Forms;

use Modules\Files\Models\BannersPhotos;
use Modules\Files\Models\MainBanners;
use Modules\Files\Models\TmpPhoto;
use Modules\Files\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\Pure;
use Modules\Orders\Models\Orders;
use Modules\Orders\Services\OrderService;
use Modules\System\Forms\AbstractForm;
use Modules\System\Forms\Inputs\InputText;
use Modules\System\Forms\Inputs\InputTextarea;
use Modules\System\Forms\Inputs\Select;

//use Modules\Users\Models\User;

//use Modules\Users\Models\User;

class FilesForm extends AbstractForm
{
    public Orders $orders;

    public function __construct()
    {
        $this->orders = (new Orders());
    }

    /**
     * @inheritDoc
     */
    public function form(): AbstractForm
    {
        $seasons = OrderService::getSeasons();
        $this->form = [
            /**
             * Основные поля
             */
            'season_id' => (new Select())
                ->setLabel('Сезон')
                ->setValidationRule('bail|required')
                ->setNameAndId('season_id')
                ->setValue(count($seasons) > 0 ? $seasons[0]->id : '')
                ->setItems(
                    static function () {
                        return OrderService::getSeasons();
                    }
                )
                ->get(),
            /**
             * Блок с файлом
             */
            'file'      => [
                'name'  => 'Загрузочный файл',
                'items' => [],
            ],
        ];

        // Добавляем дополнительные служебные поля для существующих объектов
        return $this;
    }
}
