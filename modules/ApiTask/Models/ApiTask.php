<?php

namespace Modules\ApiTask\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_id',
        'status',
    ];

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('status', '!=', true);
    }
}
