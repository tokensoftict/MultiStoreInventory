<?php

namespace App\Imports;

use App\Models\Stock;
use App\Models\StockTaking;
use App\Models\StockTakingItem;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockTakingItemImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    protected $stockTaking;

    public function __construct(StockTaking $stockTaking)
    {
        $this->stockTaking = $stockTaking;
    }

    public function model(array $row)
    {
        $stock = Stock::find($row['id']);

        if(!$stock) return null;

        $this->stockTaking->status = 'Uploaded';
        $this->stockTaking->update();
        $row['counted_bundle_quantity'] = empty($row['counted_bundle_quantity']) ? 0 : $row['counted_bundle_quantity'];
        $row['counted_yard_quantity'] = empty($row['counted_yard_quantity']) ? 0 : $row['counted_yard_quantity'];
        return new StockTakingItem([
            'name' => $this->stockTaking->name,
            'stock_id' =>$row['id'],
            'available_quantity' =>$stock->getCustomAvailableQuantityAttribute($this->stockTaking->warehousestore->packed_column),
            'available_yard_quantity' => $stock->getCustomAvailableYardQuantityAttribute($this->stockTaking->warehousestore->yard_column),
            'counted_available_quantity' => $row['counted_bundle_quantity'],
            'counted_yard_quantity' => $row['counted_yard_quantity'],
            'available_quantity_diff' => ( $row['counted_bundle_quantity'] - $stock->available_quantity),
            'available_yard_quantity_diff' => ($row['counted_yard_quantity'] - $stock->available_yard_quantity),
            'stock_taking_id' => $this->stockTaking->id,
            'warehousestore_id' => $this->stockTaking->warehousestore_id,
            'user_id' => auth()->id(),
            'status' => 'Uploaded',
            'date' => $this->stockTaking->date
        ]);

    }

}
