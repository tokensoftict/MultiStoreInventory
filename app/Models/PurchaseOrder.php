<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\PoItem;
use Carbon\Carbon;
use http\Env\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;


/**
 * Class PurchaseOrder
 *
 * @property int $id
 * @property float $total
 * @property int|null $supplier_id
 * @property Carbon|null $date_created
 * @property Carbon|null $date_approved
 * @property Carbon|null $date_completed
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $approved_by
 * @property string|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User|null $user
 * @property Supplier|null $supplier
 * @property Collection|PurchaseOrderItem[] $purchase_order_items
 *
 * @package App\Models
 */
class PurchaseOrder extends Model
{
    use LogsActivity;

    protected $table = 'purchase_orders';

    protected $casts = [
        'supplier_id' => 'int',
        'created_by' => 'int',
        'updated_by' => 'int',
        'approved_by' => 'int',
        'total' => 'float'
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
        'total',
        'status',
        'created_by',
        'updated_by',
        'approved_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function created_user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approved_user()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase_order_items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }


    public function complete(){
        if($this->status  == "COMPLETE") return redirect()->route('purchaseorders.index')->with('success','Purchase Order has been completed successfully!');
        foreach ($this->purchase_order_items()->get() as $purchase){

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
        }

        $this->status = "COMPLETE";
        $this->update();

        SupplierCreditPaymentHistory::create(
            [
                'user_id' => \auth()->id(),
                'supplier_id' =>$this->supplier_id,
                'purchase_order_id' => $this->id,
                'payment_method_id' => NULL,
                'payment_info' => "",
                'amount' => -$this->total,
                'payment_date' =>date('Y-m-d',strtotime($this->date_created)),
            ]
        );

        return redirect()->route('purchaseorders.index')->with('success','Purchase Order has been completed successfully!');
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

        return redirect()->route('purchaseorders.index')->with('success','Purchase Order has been updated successfully!');
    }

    public static function createPurchaseOrder($request){
        $po =PurchaseOrder::create([
            'supplier_id' => $request->supplier_id,
            'date_created' => $request->date_created,
            'date_approved' => $request->date_created,
            'date_completed' => $request->date_created,
            'status' => "DRAFT",
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

        return redirect()->route('purchaseorders.index')->with('success','Purchase Order has been created successfully!');
    }


}
