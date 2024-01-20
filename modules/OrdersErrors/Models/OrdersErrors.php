<?php

namespace Modules\OrdersErrors\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdersErrors extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
    ];
}
