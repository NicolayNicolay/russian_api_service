<?php

namespace Modules\Sms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Modules\Orders\Models\Orders;

class Sms extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'status',
    ];
    protected $casts = [
        'created_at' => 'date:d.m.Y H:i:s',
    ];
    protected $appends = [
        'status_name',
        'status_class',
        'short_text',
    ];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Orders::class, 'sms_orders', 'sms_id', 'order_id');
    }

    public function getPhones(): Collection
    {
        return $this->orders()->pluck('phone_relatives');
    }

    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            1 => 'Отправлено',
            default => 'Ожидает отправки'
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            1 => 'badge-success',
            default => 'badge-moderate'
        };
    }

    public function getShortTextAttribute(): string
    {
        if ($this->text) {
            return strlen($this->text) > 200 ? substr($this->text, 0, 200) . '...' : $this->text;
        } else {
            return '';
        }
    }
}
