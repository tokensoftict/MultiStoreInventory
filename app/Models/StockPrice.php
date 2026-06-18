<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockPrice extends Model
{
    protected $table = 'stock_prices';

    protected $fillable = [
        'stock_id',
        'price_category_id',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:4'
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function priceCategory()
    {
        return $this->belongsTo(PriceCategory::class, 'price_category_id');
    }
}
