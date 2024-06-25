<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Payment
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $customer_id
 * @property string $invoice_number
 * @property string $invoice_type
 * @property int $invoice_id
 * @property float $subtotal
 * @property float $total_paid
 * @property Carbon|null $payment_time
 * @property Carbon|null $payment_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Customer|null $customer
 * @property User|null $user
 * @property Collection|Invoice[] $invoices
 * @property Collection|PaymentMethodTable[] $payment_method_tables
 *
 * @package App\Models
 */
class Payment extends Model
{


    protected $table = 'payments';

    protected $casts = [
        'user_id' => 'int',
        'customer_id' => 'int',
        'warehousestore_id' => 'int',
        'invoice_id' => 'int',
        'subtotal' => 'float',
        'total_paid' => 'float'
    ];

    protected $dates = [
        'payment_time',
        'payment_date'
    ];

    protected $fillable = [
        'user_id',
        'customer_id',
        'warehousestore_id',
        'department',
        'invoice_number',
        'invoice_type',
        'invoice_id',
        'subtotal',
        'total_paid',
        'payment_time',
        'payment_date'
    ];

    public function warehousestore()
    {
        return $this->belongsTo(Warehousestore::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment_method_tables()
    {
        return $this->hasMany(PaymentMethodTable::class);
    }


    public function getTotalPaidAttribute()
    {
        return $this->paymentMethodTable->sum(function($payment){
            if($payment['payment_method_id'] !== 4){
                return $payment['amount'];
            }else{
                return 0;
            }
        });
    }

    public function invoice(){

        return $this->morphTo();
    }


    public static function createPayment($paymentInformation){

        $invoiceType = ($paymentInformation['type'] == "Reservation" ? "App\\Models\\BookingReservation" : "App\\Models\\Invoice");

        if($paymentInformation['type'] != "Reservation")
        {
            Payment::where('invoice_type',$invoiceType)->where('invoice_id',$paymentInformation['invoice']->id)->delete();
        }

        $payment = Payment::create([
            'user_id' => auth()->id(),
            'customer_id' => $paymentInformation['invoice']->customer_id,
            'invoice_number' => $paymentInformation['invoice']->invoice_number,
            'invoice_id' => $paymentInformation['invoice']->id,
            'invoice_type'=>$invoiceType,
            'department' => auth()->user()->department,
            'warehousestore_id' => getActiveStore()->id,
            'subtotal' => $paymentInformation['invoice']->sub_total - $paymentInformation['invoice']->discount_amount,
            'total_paid' => $paymentInformation['invoice']->sub_total - $paymentInformation['invoice']->discount_amount,
            'payment_time' => Carbon::now()->toTimeString(),
            'payment_date' => $paymentInformation['invoice']->invoice_date,
        ]);
        if( $paymentInformation['payment_info']['payment_method_id'] == "split_method")
        {


            $invoice_amount =  $paymentInformation['invoice']->sub_total - $paymentInformation['invoice']->discount_amount;

            $total_amount_paid = 0;
            foreach($paymentInformation['payment_info']['split_method'] as $pmthod=>$amount)
            {
                if(is_numeric($amount)) {
                    $total_amount_paid += $amount;
                }
            }

            if($total_amount_paid < $invoice_amount){
                $paymentInformation['payment_info']['split_method'][4] = ($invoice_amount - $total_amount_paid);
                $paymentInformation['payment_info']['payment_info_data'][4] = [
                    'payment_method_id' => 4,
                    'credit' => "credit"
                ];
            }
            else if($total_amount_paid > $invoice_amount)
            {

                $paymentInformation['payment_info']['split_method'][4] = -($total_amount_paid- $invoice_amount );
                // this is an over payment for this invoice
                $paymentInformation['payment_info']['payment_info_data'][4] = [
                    'payment_method_id' => 4,
                    'credit' => "credit"
                ];
            }



            $splits = [];

            foreach($paymentInformation['payment_info']['split_method'] as $pmthod=>$amount)
            {
                if(intval($amount) > 0) {
                    if ($pmthod != 4) {
                        $splits[] = new PaymentMethodTable([
                            'user_id' => auth()->id(),
                            'customer_id' => $paymentInformation['invoice']->customer_id,
                            'payment_method_id' => $pmthod,
                            'invoice_id' => $paymentInformation['invoice']->id,
                            'invoice_type' => $invoiceType,
                            'department' => auth()->user()->department,
                            'warehousestore_id' => getActiveStore()->id,
                            'payment_date' => $payment->payment_date,
                            'amount' => $amount,
                            'payment_info' => json_encode($paymentInformation['payment_info']['payment_info_data'][$pmthod])
                        ]);
                    } else {
                        $credit_payment_info = [
                            'user_id' => auth()->id(),
                            'customer_id' => $paymentInformation['invoice']->customer_id,
                            'payment_method_id' => $pmthod,
                            'invoice_id' => $paymentInformation['invoice']->id,
                            'invoice_type' =>$invoiceType,
                            'warehousestore_id' => getActiveStore()->id,
                            'payment_date' => $payment->payment_date,
                            'amount' => $amount,
                            'payment_info' => json_encode($paymentInformation['payment_info']['payment_info_data'][$pmthod])
                        ];
                    }
                }
            }
            $payment->payment_method_tables()->saveMany($splits);

            if(isset($credit_payment_info)){
                $payment_method_id = $payment->payment_method_tables()->save(new PaymentMethodTable($credit_payment_info));

                $credit_log = [
                    'payment_id' => $payment->id,
                    'user_id' => auth()->id(),
                    'payment_method_id' => $payment_method_id->id,
                    'customer_id' =>$paymentInformation['invoice']->customer_id,
                    'invoice_number' => $paymentInformation['invoice']->invoice_number,
                    'invoice_id' => $paymentInformation['invoice']->id,
                    'amount' => -($payment_method_id->amount),
                    'payment_date' => $payment->payment_date,
                ];

                CreditPaymentLog::create($credit_log);

            }

        }else {
            $payment_method_id = $payment->payment_method_tables()->save(new PaymentMethodTable([
                'user_id' => auth()->id(),
                'customer_id' => $paymentInformation['invoice']->customer_id,
                'payment_method_id' => $paymentInformation['payment_info']['payment_method_id'],
                'invoice_id' => $paymentInformation['invoice']->id,
                'invoice_type' => $invoiceType,
                'department' => auth()->user()->department,
                'warehousestore_id' => getActiveStore()->id,
                'payment_date' => $payment->payment_date,
                'amount' => $paymentInformation['invoice']->sub_total - $paymentInformation['invoice']->discount_amount,
                'payment_info' => json_encode(Arr::get($paymentInformation, 'payment_info'))
            ]));

            if($paymentInformation['payment_info']['payment_method_id'] == 4)
            {
                $credit_log = [
                    'payment_id' => $payment->id,
                    'user_id' => auth()->id(),
                    'payment_method_id' => $payment_method_id->id,
                    'customer_id' => $paymentInformation['invoice']->customer_id,
                    'invoice_number' => $paymentInformation['invoice']->invoice_number,
                    'invoice_id' => $paymentInformation['invoice']->id,
                    'amount' => -($payment_method_id->amount),
                    'payment_date' => $payment->payment_date,
                ];
                CreditPaymentLog::create($credit_log);
            }

        }

        return $payment;
    }


    public static function validateCreditLimit($paymentInformation, $reports){
        if($reports instanceof Invoice){
            $totals['total_invoice_total_selling'] = ($reports->sub_total - $reports->discount_amount);
        }else{
            $totals = Invoice::calculateInvoiceTotal($reports);
        }
        $total = 0;
        $total_amount_paid = 0;
        if( $paymentInformation['payment_info']['payment_method_id'] == "split_method")
        {
            $total+=$totals['total_invoice_total_selling'];
            $total_amount_paid = 0;

            foreach($paymentInformation['payment_info']['split_method'] as $pmthod=>$amount)
            {
                if(is_numeric($amount)) {
                    $total_amount_paid += $amount;
                }
            }

            $total = $total - $total_amount_paid;

        }else if($paymentInformation['payment_info']['payment_method_id'] == 4){
            $total+=$totals['total_invoice_total_selling'];
        }

        if($total === 0) return false;

        if($reports instanceof Invoice){
            $customer =  Customer::find($reports->customer_id);
        }else {
            $customer = Customer::find(request()->get('customer_id'));
        }
        $credit_limit = (int)$customer->credit_limit;

        $total_credit = (int)$customer->credit_balance;

        if($credit_limit === 0){ // credit limit has not been set
            return false;
        }

        if($total_credit > 0) return false;

        if($total_credit < 0){
            $total_credit = -$total_credit;
        }
        $total_credit = $total_credit+$total;


        if($total_credit > $credit_limit){
            return true;
        }

        return false;
    }

}
