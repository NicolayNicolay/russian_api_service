<?php

namespace Modules\Seasons\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seasons extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sort', 'active'];

    public function scopeActive(Builder $builder): Builder
    {
        return $builder
            ->where('active', 1);
    }
}
