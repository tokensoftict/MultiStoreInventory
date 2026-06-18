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

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_price_category_mappers', 'price_category_id', 'user_id')->withTimestamps();
    }
}
