<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Classes\Settings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Deposit
 * 
 * @property int $id
 * @property string $deposit_number
 * @property string $deposit_paper_number
 * @property string $department
 * @property int|null $warehousestore_id
 * @property int|null $customer_id
 * @property string|null $discount_type
 * @property float|null $discount_amount
 * @property string $status
 * @property float $sub_total
 * @property float $total_amount_paid
 * @property float $total_profit
 * @property float $total_cost
 * @property float $vat
 * @property float $vat_amount
 * @property int|null $created_by
 * @property int|null $last_updated_by
 * @property Carbon $deposit_date
 * @property Carbon $deposit_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 * @property Customer|null $customer
 * @property Warehousestore|null $warehousestore
 * @property Collection|DepositItem[] $deposit_items
 *
 * @package App\Models
 */
class Deposit extends Model
{
	protected $table = 'deposits';

	protected $casts = [
		'warehousestore_id' => 'int',
		'customer_id' => 'int',
		'discount_amount' => 'float',
		'sub_total' => 'float',
		'total_amount_paid' => 'float',
		'total_profit' => 'float',
		'total_cost' => 'float',
		'vat' => 'float',
		'vat_amount' => 'float',
		'created_by' => 'int',
		'last_updated_by' => 'int'
	];

	protected $dates = [
		'deposit_date',
		'deposit_time'
	];

	protected $fillable = [
		'deposit_number',
		'deposit_paper_number',
		'department',
		'warehousestore_id',
		'customer_id',
		'discount_type',
		'discount_amount',
		'status',
		'sub_total',
		'total_amount_paid',
		'total_profit',
		'total_cost',
		'vat',
		'vat_amount',
		'created_by',
		'last_updated_by',
		'deposit_date',
		'deposit_time'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'last_updated_by');
	}

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	public function warehousestore()
	{
		return $this->belongsTo(Warehousestore::class);
	}

    public function created_user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
	public function deposit_items()
	{
		return $this->hasMany(DepositItem::class);
	}

    public static function createDeposit($request, $reports)
    {
        $totals = self::calculateDepositTotal($reports);

        $deposit_data = [
            'deposit_number'=>time(),
            'deposit_paper_number' => $request->get('invoice_paper_number'),
            'customer_id'=> $request->get('customer_id'),
            'discount_type'=> "none",
            'discount_amount' => 0,
            'department'=> auth()->user()->department,
            'warehousestore_id' => getActiveStore()->id,
            'status'=>$request->get('status'),
            'sub_total' => $totals['total_deposit_total_selling'],
            'total_amount_paid' => 0,
            'total_profit' =>  $totals['total_deposit_total_profit'],
            'total_cost' => $totals['total_deposit_total_cost'],
            'vat' => 0,
            'vat_amount' => 0,
            'created_by' => auth()->id(),
            'last_updated_by' =>auth()->id(),
            'deposit_date' =>  $request->get('date'),
            'deposit_time' =>Carbon::now()->toTimeString(),
        ];

        $deposit = Deposit::create($deposit_data);

        $deposit_items_data = self::prepareDepositItemData(
            $reports,
            $request->get('customer_id'),
            $request->get('status'),
            $request->get('date'),
            Carbon::now()->toTimeString(),
            $deposit
        );


        foreach ($deposit_items_data as $deposit_items_datum) {
            $deposit
                ->deposit_items()
                ->save($deposit_items_datum);
        }

        return $deposit;
    }

    public static function calculateDepositTotal($validationReports)
    {
        $stocks = $validationReports['data'];

        $depositTotal = [];

        $total_deposit_total_selling = 0;
        $total_deposit_total_cost = 0;
        $total_deposit_total_profit = 0;

        foreach ($stocks as $key=>$stock) {

            $depositTotal[$key]['total_selling_price']  =  $stock['prods']['price'] * $stock['prods']['qty'];
            $depositTotal[$key]['total_cost_price']  =  $stock['prods']['cost_price'] * $stock['prods']['qty'];
            $depositTotal[$key]['total_profit'] =  $depositTotal[$key]['total_selling_price'] -  $depositTotal[$key]['total_cost_price'];
            $total_deposit_total_selling += $depositTotal[$key]['total_selling_price'];
            $total_deposit_total_cost += $depositTotal[$key]['total_cost_price'] ;
            $total_deposit_total_profit+=$depositTotal[$key]['total_profit'];
        }

        return [
            'total_deposit_total_selling'=>$total_deposit_total_selling,
            'total_deposit_total_cost' =>$total_deposit_total_cost,
            'total_deposit_total_profit' =>$total_deposit_total_profit
        ];
    }

    public static function preparedepositItemData($validationReports, $customer_id, $status, $deposit_date,$sales_time, $deposit)
    {
        $stocks = $validationReports['data'];

        $depositItems = [];
        foreach ($stocks as $key=>$stock) {

            $total_selling_price  =  $stock['prods']['price'] * $stock['prods']['qty'];
            $total_cost_price  =  $stock['prods']['cost_price'] * $stock['prods']['qty'];
            $total_profit =   $total_selling_price -  $total_cost_price;

            $depositItems[$key] =  new DepositItem([
                'deposit_id'=> $deposit->id,
                'stock_id'=>$key,
                'quantity'=>$stock['prods']['qty'],
                'customer_id'=>$customer_id,
                'department'=> auth()->user()->department,
                'status' => $status,
                'added_by'=> auth()->id(),
                'warehousestore_id' => getActiveStore()->id,
                'deposit_date' =>$deposit_date,
                'store' => $stock['prods']['type'],
                'deposit_time' =>$sales_time,
                'cost_price'=>($deposit->sub_total < 0 ? -($stock['prods']['cost_price']) : ($stock['prods']['cost_price'])),
                'selling_price' =>($deposit->sub_total < 0 ? -($stock['prods']['price']) : ($stock['prods']['price'])),
                'profit'=>($deposit->sub_total < 0 ? ($stock['prods']['price'] - $stock['prods']['cost_price']) : ($stock['prods']['price'] - $stock['prods']['cost_price'])),
                'total_selling_price' =>($deposit->sub_total < 0 ? -$total_selling_price : $total_selling_price),
                'total_cost_price' => ($deposit->sub_total < 0 ? -$total_cost_price : $total_cost_price),
                'total_profit'=>($deposit->sub_total < 0 ? -$total_profit : $total_profit),
                'discount_type'=>'none',
                'discount_amount'=>0,
            ]);

        }

        return $depositItems;
    }

    public static function validateDepositProduct($products,$store)
    {
        $status = false;
        $report = [];
        $errors = [];
        $prods = [];

        $product_ids =  array_column($products,'id');
        foreach ($products as $product){
            $prods[$product['id']] = $product;
        }

        $stocks = Stock::whereIn('id',$product_ids)->get();

        foreach ($stocks as $stock) {

            $report[$stock->id]['batches'] =  [];
            $report[$stock->id]['stock'] = $stock;
            $report[$stock->id]['prods'] = $prods[$stock->id];


            if($prods[$stock->id]['type'] === "yard_quantity"){
                if(app(Settings::class)->get('allow_selling_below_cost_price') == "0" and (float)$stock->yard_cost_price >= (float)$prods[$stock->id]['price']){
                    $status = true;
                    $errors[$stock->id] = $stock->name." can not be sell under cost price ".number_format($stock->yard_cost_price,2 );
                }
            }


            if($prods[$stock->id]['type'] === "quantity"){
                if(app(Settings::class)->get('allow_selling_below_cost_price') == "0" and (float)$stock->cost_price >= (float)$prods[$stock->id]['price']){
                    $status = true;
                    $errors[$stock->id] = $stock->name." can not be sell under cost price ".number_format($stock->cost_price,2 );
                }
            }
        }

        return ['status'=> $status, 'data'=>$report,'errors'=> $errors];
    }

    public static function updateDeposit($request, $reports, $deposit)
    {
        if($deposit!=false) {
            $deposit->deposit_items()->delete();
        }


        $totals = self::calculateDepositTotal($reports);


        $deposit->customer_id = $request->get('customer_id');
        $deposit->status = $request->get('status');
        $deposit->sub_total = $totals['total_invoice_total_selling'];
        $deposit->total_amount_paid = 0;
        $deposit->total_profit = $totals['total_invoice_total_profit'];
        $deposit->total_cost = $totals['total_invoice_total_cost'];
        $deposit->last_updated_by = auth()->id();

        $deposit->update();


        $invoice_items_data = self::preparedepositItemData(
            $reports,
            $request->get('customer_id'),
            $request->get('status'),
            $request->get('date'),
            Carbon::now()->toTimeString(),
            $deposit
        );

        foreach ($invoice_items_data as $key=>$invoice_items_datum){
            $deposit
                ->invoice_items()
                ->save($invoice_items_datum);
        }


        return $deposit;
    }
}
