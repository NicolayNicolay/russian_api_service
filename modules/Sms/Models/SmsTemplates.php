<?php

namespace Modules\Sms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTemplates extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'text',
    ];

    protected $casts = [
        'created_at' => 'date:d.m.Y H:i:s',
    ];

    protected $appends = [
        'short_text',
    ];

    public function getShortTextAttribute(): string
    {
        if ($this->text) {
            return strlen($this->text) > 200 ? substr($this->text, 0, 200) . '...' : $this->text;
        } else {
            return '';
        }
    }
}
