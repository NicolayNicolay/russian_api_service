<?php

namespace Modules\OrderTracking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PostResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'stage',
        'state'
    ];
}
