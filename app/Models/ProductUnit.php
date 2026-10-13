<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A unit of a product used only by stores in the multi-level quantity system.
 * level: 1 = Carton, 2 = Packet, 3 = Piece (the base unit).
 *
 * @property int $id
 * @property int $stock_id
 * @property string $name
 * @property int $level
 * @property int|null $parent_unit_id
 * @property float|null $units_per_parent
 * @property float $cost_price
 * @property float $selling_price
 * @property bool $status
 */
class ProductUnit extends Model
{
    const LEVEL_CARTON = 1;
    const LEVEL_PACKET = 2;
    const LEVEL_PIECE = 3;

    protected $table = 'product_units';

    protected $casts = [
        'stock_id' => 'int',
        'level' => 'int',
        'parent_unit_id' => 'int',
        'units_per_parent' => 'float',
        'cost_price' => 'float',
        'selling_price' => 'float',
        'status' => 'bool',
    ];

    protected $fillable = [
        'stock_id',
        'name',
        'level',
        'parent_unit_id',
        'units_per_parent',
        'cost_price',
        'selling_price',
        'status',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function parent()
    {
        return $this->belongsTo(ProductUnit::class, 'parent_unit_id');
    }
}
