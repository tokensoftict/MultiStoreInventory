<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceCategory extends Model
{
    protected $table = 'price_categories';

    protected $casts = [
        'status' => 'bool'
    ];

    protected $fillable = [
        'name',
        'price_type',
        'description',
        'status'
    ];

    public function stockPrices()
    {
        return $this->hasMany(StockPrice::class, 'price_category_id');
    }
}
