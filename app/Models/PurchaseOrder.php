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

                $purchase->stock->cost_price = $purchase->cost_price;
                $purchase->stock->selling_price = $purchase->selling_price;
                $purchase->stock->update();
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
            $route = 'purchaseorders.index';
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
            $route = 'purchaseorders.index';
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
            $route = 'purchaseorders.index';
        }else{
            $route = 'purchaseorders.returns';
        }

        return redirect()->route($route)->with('success','Purchase Order has been created successfully!');
    }


}
