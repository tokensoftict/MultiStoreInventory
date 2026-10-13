<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PurchaseOrder
 * 
 * @property int $id
 * @property int|null $supplier_id
 * @property Carbon|null $date_created
 * @property Carbon|null $date_approved
 * @property Carbon|null $date_completed
 * @property int|null $warehousestore_id
 * @property float $total
 * @property string $status
 * @property string $purchase_order_invoice_number
 * @property string $type
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $approved_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 * @property Supplier|null $supplier
 * @property Warehousestore|null $warehousestore
 * @property Collection|PurchaseOrderItem[] $purchase_order_items
 * @property Collection|SupplierCreditPaymentHistory[] $supplier_credit_payment_histories
 *
 * @package App\Models
 */
class PurchaseOrder extends Model
{
	protected $table = 'purchase_orders';

	protected $casts = [
		'supplier_id' => 'int',
		'warehousestore_id' => 'int',
		'total' => 'float',
		'created_by' => 'int',
		'updated_by' => 'int',
		'approved_by' => 'int'
	];

	protected $dates = [
		'date_created',
		'date_approved',
		'date_completed'
	];

	protected $fillable = [
		'supplier_id',
		'date_created',
		'date_approved',
		'date_completed',
		'warehousestore_id',
		'total',
		'status',
		'created_by',
		'updated_by',
		'approved_by',
        'type',
        'purchase_order_invoice_number'
	];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
	public function updated_user()
	{
		return $this->belongsTo(User::class, 'updated_by');
	}

    public function created_user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

	public function supplier()
	{
		return $this->belongsTo(Supplier::class);
	}

	public function warehousestore()
	{
		return $this->belongsTo(Warehousestore::class);
	}

	public function purchase_order_items()
	{
		return $this->hasMany(PurchaseOrderItem::class);
	}

	public function supplier_credit_payment_histories()
	{
		return $this->hasMany(SupplierCreditPaymentHistory::class);
	}

    public function complete(){
        if($this->status  == "COMPLETE") return redirect()->route('purchaseorders.index')->with('success','Purchase Order has been completed successfully!');
        foreach ($this->purchase_order_items()->get() as $purchase){

            if($purchase->type === "PURCHASE") {
                $batch = Stockbatch::create([
                    'received_date' => $purchase->date_created,
                    'expiry_date' => NULL,
                    $purchase->store => $purchase->qty,
                    //'cost_price' =>$purchase->cost_price,
                    // 'selling_price' =>$purchase->selling_price,
                    'supplier_id' => $this->supplier_id,
                    'stock_id' => $purchase->stock_id
                ]);
                $purchase->stockbatch_id = $batch->id;
                $purchase->update();

                if (empty($purchase->unit_id)) {
                    $purchase->stock->cost_price = $purchase->cost_price;
                    $purchase->stock->selling_price = $purchase->selling_price;
                    $purchase->stock->update();
                } else {
                    // multi-level: refresh the received unit's own prices (levels stay independent)
                    $units = \App\Services\StockUnitService::units($purchase->stock_id);
                    $mult = \App\Services\InventoryQuantityService::multiplier($units, $purchase->unit_id);
                    \App\Models\ProductUnit::where('id', $purchase->unit_id)->update([
                        'cost_price' => $purchase->unit_price,
                        'selling_price' => round($purchase->selling_price * $mult, 4),
                    ]);
                }
            }else{
                $batches =  $purchase->stock->getSaleableBatches( $purchase->store, $purchase->qty);
                $purchase->stock->removeSaleableBatches($batches);
                foreach ($batches as $key=>$batch){
                    $purchase->stockbatch_id =$key;
                }

                $purchase->update();

            }

        }

        $this->status = "COMPLETE";
        $this->update();

        SupplierCreditPaymentHistory::create(
            [
                'user_id' => \auth()->id(),
                'supplier_id' =>$this->supplier_id,
                'purchase_order_id' => $this->id,
                'payment_method_id' => NULL,
                'payment_info' => ($this->type == "PURCHASE" ? "" : $this->purchase_order_invoice_number),
                'amount' => ($this->type == "PURCHASE" ? -$this->total : $this->total),
                'payment_date' =>date('Y-m-d',strtotime($this->date_created)),
            ]
        );

        if($this->type == "PURCHASE"){
            if(config('app.go_to_list_product') === true) {
                $route = 'purchaseorders.list_product';
            } else {
                $route = 'purchaseorders.index';
            }
        }else{
            $route = 'purchaseorders.returns';
        }

        return redirect()->route($route)->with('success','Purchase Order has been completed successfully!');
    }


    public static function updatePurchaseOrder($id,$request)
    {
        $po = PurchaseOrder::findorfail($id);

        $po->purchase_order_items()->delete();

        $po->update([
            'supplier_id' => $request->supplier_id,
            'date_created' => $request->date_created,
            'date_approved' => $request->date_created,
            'date_completed' => $request->date_created,
            'warehousestore_id' => getStoreIDFromName($request->get('store')),
            'type' => $request->type,
            'purchase_order_invoice_number' => $request->purchase_order_invoice_number,
            'status' => "DRAFT",
            'updated_by' => auth()->id(),
            'approved_by' => auth()->id()
        ]);

        $total = 0;
        $stocks = $request->get('stock_id');
        $qty = $request->get('qty');
        $cost_price = $request->get('cost_price');
        $selling_price = $request->get('selling_price');
        $batch = NULL;
        // Multi-level stores: items are created per unit (original unit quantities preserved)
        $mlTotal = self::createMultiLevelItems($po, $request);
        if ($mlTotal !== null) {
            $total = $mlTotal;
            $stocks = [];
        }
        foreach($stocks as $key=>$value){
            $total += ($cost_price[$key] * $qty[$key]);
            PurchaseOrderItem::create([
                'stock_id'=>$value,
                'qty'=>$qty[$key],
                'added_by'=>auth()->id(),
                'store'=>$request->get('store'),
                'stockbatch_id'=>NULL,
                'type' => $request->type,
                'purchase_order_invoice_number' => $request->purchase_order_invoice_number,
                'cost_price'=>$cost_price[$key],
                'selling_price' => $selling_price[$key],
                'purchase_order_id'=>$po->id
            ]);
        }

        $po->total = $total;
        $po->update();

        if($request->status == "COMPLETE"){
            return $po->complete();
        }

        if($po->type == "PURCHASE"){
            if(config('app.go_to_list_product') === true) {
                $route = 'purchaseorders.list_product';
            } else {
                $route = 'purchaseorders.index';
            }
        }else{
            $route = 'purchaseorders.returns';
        }

        return redirect()->route($route)->with('success','Purchase Order has been updated successfully!');
    }

    public static function createPurchaseOrder($request){
        $po =PurchaseOrder::create([
            'supplier_id' => $request->supplier_id,
            'date_created' => $request->date_created,
            'date_approved' => $request->date_created,
            'date_completed' => $request->date_created,
            'type' => $request->type,
            'purchase_order_invoice_number' => $request->purchase_order_invoice_number,
            'status' => "DRAFT",
            'warehousestore_id' => getStoreIDFromName($request->get('store')),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'approved_by' => auth()->id(),
        ]);
        $total = 0;
        $stocks = $request->get('stock_id');
        $qty = $request->get('qty');
        $cost_price = $request->get('cost_price');
        $selling_price = $request->get('selling_price');
        $batch = NULL;
        // Multi-level stores: items are created per unit (original unit quantities preserved)
        $mlTotal = self::createMultiLevelItems($po, $request);
        if ($mlTotal !== null) {
            $total = $mlTotal;
            $stocks = [];
        }
        foreach($stocks as $key=>$value){
            $total += ($cost_price[$key] * $qty[$key]);
            PurchaseOrderItem::create([
                'stock_id'=>$value,
                'qty'=>$qty[$key],
                'added_by'=>auth()->id(),
                'store'=>$request->get('store'),
                'stockbatch_id'=>NULL,
                'type' => $request->type,
                'purchase_order_invoice_number' => $request->purchase_order_invoice_number,
                'cost_price'=>$cost_price[$key],
                'selling_price' => $selling_price[$key],
                'purchase_order_id'=>$po->id
            ]);
        }

        $po->total = $total;
        $po->update();

        if($request->status == "COMPLETE"){
            return $po->complete();
        }

        if($po->type == "PURCHASE"){
            if(config('app.go_to_list_product') === true) {
                $route = 'purchaseorders.list_product';
            } else {
                $route = 'purchaseorders.index';
            }

        }else{
            $route = 'purchaseorders.returns';
        }

        return redirect()->route($route)->with('success','Purchase Order has been created successfully!');
    }

    /**
     * Create purchase/return items for a multi-level store. Returns the PO total, or null when the
     * store is not multi-level (the legacy path then runs untouched).
     * One item row per unit: qty = base pieces, unit_* keeps what was actually received.
     */
    private static function createMultiLevelItems($po, $request)
    {
        $store = \App\Services\StockUnitService::storeByColumn($request->get('store'));
        if (!$store || !\App\Services\StockUnitService::isMulti($store->id)) {
            return null;
        }

        $stockIds = (array)$request->get('stock_id', []);
        $unitIds = (array)$request->get('unit_id', []);
        $qtys = (array)$request->get('qty', []);
        $costs = (array)$request->get('cost_price', []);
        $sells = (array)$request->get('selling_price', []);

        $total = 0;
        foreach ($stockIds as $key => $stockId) {
            $unitId = $unitIds[$key] ?? null;
            $unitQty = (float)($qtys[$key] ?? 0);
            $unitCost = (float)($costs[$key] ?? 0);
            $unitSell = (float)($sells[$key] ?? 0);

            $units = \App\Services\StockUnitService::units($stockId);
            $unit = null;
            foreach ($units as $u) {
                if ((string)$u['id'] === (string)$unitId) {
                    $unit = $u;
                }
            }
            $errors = [];
            if (!$unit) {
                $errors['stock_id'] = 'Select a valid unit for every item of a multi-level store.';
            } elseif ($unitQty <= 0 || $unitCost < 0 || $unitSell < 0) {
                $errors['qty'] = 'Quantity must be greater than zero and prices cannot be negative.';
            }
            if ($errors) {
                throw \Illuminate\Validation\ValidationException::withMessages($errors);
            }

            $mult = \App\Services\InventoryQuantityService::multiplier($units, $unit['id']);
            $base = \App\Services\InventoryQuantityService::toBase($units, $unit['id'], $unitQty);
            $total += $unitCost * $unitQty;

            PurchaseOrderItem::create([
                'stock_id' => $stockId,
                'qty' => $base,
                'added_by' => auth()->id(),
                'store' => $store->yard_column,
                'stockbatch_id' => NULL,
                'type' => $request->type,
                'purchase_order_invoice_number' => $request->purchase_order_invoice_number,
                'cost_price' => $unitCost / $mult,
                'selling_price' => $unitSell / $mult,
                'purchase_order_id' => $po->id,
                'unit_id' => $unit['id'],
                'unit_name' => $unit['name'],
                'unit_quantity' => $unitQty,
                'unit_price' => $unitCost,
                'base_quantity' => $base,
            ]);
        }
        return $total;
    }

}
