<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $trading_symbol
 * @property float $price
 */
class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'trading_symbol',
        'name',
        'price',
        'created_at'
    ];
}
