<?php

namespace Modules\Orders\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Orders\Filters\OrdersFilter;
use Modules\OrdersErrors\Models\OrdersErrors;
use Modules\OrderTracking\Models\PostResponse;
use Modules\Seasons\Models\Seasons;
use Modules\Users\Models\User;
use EloquentFilter\Filterable;

class Orders extends Model
{
    use HasFactory;
    use Filterable;

    protected $fillable = [
        'code',
        'lot_number',
        'order_number',
        'season_id',
        'fio',
        'index',
        'geo',
        'district',
        'address',
        'fio_relatives',
        'phone_relatives',
        'final',
        'payment_state',
        'processed_paid',
        'notes',
        'track',
        'status',
        'last_status_updated',
        'updated_user_id',
        'created_user_id',
        'info',
        'date_operation',
        'payment_sum',
        'payment_date',
        'payment_place_index',
        'error_id',
        'status_availability_payment',
        'availability_payment',
        'availability_payment_date',
    ];

    protected $casts = [
        'created_at' => 'date:d.m.Y H:i:s',
        'updated_at' => 'date:d.m.Y H:i:s',
        'last_status_updated' => 'date:d.m.Y H:i:s',
        'date_operation' => 'date:d.m.Y H:i:s',
        'payment_date' => 'date:d.m.Y H:i:s',
    ];

    protected $appends = [
        'status_name',
        'payment_status_name',
        'table_color',
        'avail_payment',
        'status_stage_name',
        'status_state_name',
        'payment_state_name',
    ];

    protected $guarded = ['code', 'season_id'];

    /**
     * Получение объектов с заполненными трек номерами
     */
    public function scopeTrack(Builder $builder): Builder
    {
        return $builder
            ->whereNotNull('track')
            ->where('track', '!=', '');
    }

    public function scopePaymentOrders(Builder $builder): Builder
    {
        return $builder
            ->whereNotNull('payment_state')
            ->where('payment_state', 3);
    }

    public function scopeFinal(Builder $builder): Builder
    {
        return $builder->where(
            static function (Builder $query) {
                $query->whereNotNull('final')
                    ->where('final', '!=', 0);
            }
        );
    }

    public function scopePayd(Builder $builder): Builder
    {
        return $builder->whereIn('status', config('tracking.payd_statuses'));
    }

    public function scopeProcessed(Builder $builder): Builder
    {
        return $builder->where(
            static function (Builder $query) {
                $query->whereNull('processed_paid')
                    ->orWhere(
                        static function (Builder $query) {
                            $query->whereNotNull('processed_paid')
                                ->whereNotNull('payment_state')
                                ->where('payment_state', '!=', 3);
                        }
                    );
            }
        );
    }

    public function scopeState(Builder $builder, array $codes): Builder
    {
        return $builder->whereHas(
            'statusPost',
            static function (Builder $query) use ($codes) {
                $query->whereIn('code', $codes);
            }
        );
    }

    public function scopeNotState(Builder $builder, array $codes): Builder
    {
        return $builder->whereHas(
            'statusPost',
            static function (Builder $query) use ($codes) {
                $query->whereNotIn('code', $codes);
            }
        );
    }

    public function scopeActiveSeason(Builder $builder): Builder
    {
        return $builder->whereHas(
            'season',
            static function (Builder $query) {
                $query->where('active', 1);
            }
        );
    }

    public function scopePaymentDashboard(Builder $builder): Builder
    {
        return $builder->where(
            static function (Builder $query) {
                $query->where('payment_state', 3)
                    ->orWhere('track', '')
                    ->orWhere(
                        static function (Builder $query) {
                            $query->whereNull('availability_payment')
                                ->where('status_availability_payment', true);
                        }
                    );
            }
        );
    }

    public function season(): HasOne
    {
        return $this->hasOne(Seasons::class, 'id', 'season_id');
    }

    public function userCreated(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_user_id');
    }

    public function userUpdated(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_user_id');
    }

    public function statusPost(): ?HasOne
    {
        return $this->hasOne(PostResponse::class, 'code', 'status') ?? null;
    }

    public function error(): ?HasOne
    {
        return $this->hasOne(OrdersErrors::class, 'id', 'error_id') ?? null;
    }

    public function getStatusNameAttribute(): ?array
    {
        $order = $this->statusPost()->first();
        if ($order) {
            return [
                'state' => $order->state,
                'stage' => $order->stage,
            ];
        }
        return null;
    }

    public function getPaymentStatusNameAttribute(): ?array
    {
        if ($this->payment_state) {
            $values = config('tracking.payment_state');
            $key = array_search($this->payment_state, array_column($values, 'id'));
            return ['name' => $values[$key]['name'], 'description' => $values[$key]['description']];
        }
        return null;
    }

    public function getTableColorAttribute(): ?string
    {
        if (! $this->track) {
            return '#BCFFBC';
        }
        $payment_color = config('tracking.payment_color');
        $status_color = config('tracking.status_color');
        if ($this->payment_state && array_key_exists($this->payment_state, $payment_color)) {
            return $payment_color[$this->payment_state];
        }
        if (! $this->availability_payment && $this->status_availability_payment) {
            return '#6EFF6E';
        }
        if ($this->status && array_key_exists($this->status, $status_color)) {
            return $status_color[$this->status];
        }
        return null;
    }

    public function getAvailPaymentAttribute(): ?array
    {
        if (! $this->availability_payment && $this->status_availability_payment) {
            return [
                'name' => 'Без НП',
                'date' => $this->availability_payment_date,
            ];
        }
        return null;
    }

    public function getPaymentStateNameAttribute(): ?string
    {
        if ($this->payment_state) {
            $values = config('tracking.payment_state');
            $key = array_search($this->payment_state, array_column($values, 'id'));
            return $values[$key]['name'];
        }
        return null;
    }

    public function getStatusStageNameAttribute(): ?string
    {
        $order = $this->statusPost()->first();
        return $order?->stage;
    }

    public function getStatusStateNameAttribute(): ?string
    {
        $order = $this->statusPost()->first();
        return $order?->state;
    }

    public function modelFilter(): string
    {
        return $this->provideFilter(OrdersFilter::class);
    }

    public function scopeOrdersSort(Builder $builder, $sorting)
    {
        if ($sorting['sort_field'] !== 'code') {
            return $builder->orderBy($sorting['sort_field'], $sorting['sort_direction'])->orderBy('orders.code');
        }
        return $builder->orderBy($sorting['sort_field'], $sorting['sort_direction']);
    }
}
