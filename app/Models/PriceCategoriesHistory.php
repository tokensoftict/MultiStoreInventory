<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceCategoriesHistory extends Model
{
    protected $table = 'price_categories_history';

    public $timestamps = false;

    protected $fillable = [
        'price_category_id',
        'stock_id',
        'old_price',
        'new_price',
        'updated_by'
    ];

    protected $casts = [
        'old_price' => 'decimal:4',
        'new_price' => 'decimal:4'
    ];

    public function priceCategory()
    {
        return $this->belongsTo(PriceCategory::class, 'price_category_id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
