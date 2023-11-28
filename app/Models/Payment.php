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

    use LogsActivity;

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
            'subtotal' => $paymentInformation['invoice']->sub_total,
            'total_paid' => $paymentInformation['invoice']->sub_total,
            'payment_time' => Carbon::now()->toTimeString(),
            'payment_date' => date('Y-m-d'),
        ]);
        if( $paymentInformation['payment_info']['payment_method_id'] == "split_method")
        {


                $invoice_amount =  $paymentInformation['invoice']->sub_total;

                $total_amount_paid = 0;
                foreach($paymentInformation['payment_info']['split_method'] as $pmthod=>$amount)
                {
                    $total_amount_paid+=$amount;
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
                            'payment_date' => date('Y-m-d'),
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
                            'payment_date' => date('Y-m-d'),
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
                    'payment_date' => dailyDate(),
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
                'payment_date' => date('Y-m-d'),
                'amount' => $paymentInformation['invoice']->sub_total,
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
                    'payment_date' => dailyDate(),
                ];
                CreditPaymentLog::create($credit_log);
            }

        }

        return $payment;

    }
}
