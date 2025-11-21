<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $department
 * @property string $username
 * @property string $email
 * @property int $group_id
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property bool $status
 * @property Carbon|null $last_login
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $customer_type
 * @property int $customer_id
 *
 * @property Group $group
 * @property Collection|BookingReservationItem[] $booking_reservation_items
 * @property Collection|BookingReservation[] $booking_reservations
 * @property Collection|Cashbook[] $cashbooks
 * @property Collection|CreditPaymentLog[] $credit_payment_logs
 * @property Collection|Expense[] $expenses
 * @property Collection|Invoice[] $invoices
 * @property Collection|PaymentMethodTable[] $payment_method_tables
 * @property Collection|Payment[] $payments
 * @property Collection|PurchaseOrderItem[] $purchase_order_items
 * @property Collection|PurchaseOrder[] $purchase_orders
 * @property Collection|ReturnLog[] $return_logs
 * @property Collection|StockLogItem[] $stock_log_items
 * @property Collection|StockLogOperation[] $stock_log_operations
 * @property Collection|StockTakingItem[] $stock_taking_items
 * @property Collection|StockTaking[] $stock_takings
 * @property Collection|StockTransferItem[] $stock_transfer_items
 * @property Collection|StockTransfer[] $stock_transfers
 * @property Collection|Stock[] $stocks
 * @property Collection|SupplierCreditPaymentHistory[] $supplier_credit_payment_histories
 * @property Collection|Userstoremapper[] $userstoremappers
 * @property Collection|Userstoremapper[] $activeuserstoremappers
 * @package App\Models
 */

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivity;


    protected $casts = [
        'group_id' => 'int',
        'status' => 'bool'
    ];

    protected $dates = [
        'email_verified_at',
        'last_login'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $fillable = [
        'name',
        'username',
        'email',
        'group_id',
        'department',
        'email_verified_at',
        'password',
        'status',
        'customer_type',
        'customer_id',
        'last_login',
        'remember_token'
    ];

    public static $profile_fields = [
        'name',
        'username',
        'email',
        'password',
    ];

    public static $rules = [
        'name' => 'required|string',
        'username' => 'required|string',
    ];

    public static $rules_update = [
        'name' => 'required|string',
        'username' => 'required|string',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function booking_reservation_items()
    {
        return $this->hasMany(BookingReservationItem::class);
    }

    public function booking_reservations()
    {
        return $this->hasMany(BookingReservation::class);
    }

    public function cashbooks()
    {
        return $this->hasMany(Cashbook::class);
    }

    public function credit_payment_logs()
    {
        return $this->hasMany(CreditPaymentLog::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'voided_by');
    }

    public function payment_method_tables()
    {
        return $this->hasMany(PaymentMethodTable::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function purchase_order_items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'added_by');
    }

    public function purchase_orders()
    {
        return $this->hasMany(PurchaseOrder::class, 'updated_by');
    }

    public function return_logs()
    {
        return $this->hasMany(ReturnLog::class);
    }

    public function stock_log_items()
    {
        return $this->hasMany(StockLogItem::class);
    }

    public function stock_log_operations()
    {
        return $this->hasMany(StockLogOperation::class);
    }

    public function stock_taking_items()
    {
        return $this->hasMany(StockTakingItem::class);
    }

    public function stock_takings()
    {
        return $this->hasMany(StockTaking::class);
    }

    public function stock_transfer_items()
    {
        return $this->hasMany(StockTransferItem::class);
    }

    public function stock_transfers()
    {
        return $this->hasMany(StockTransfer::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function supplier_credit_payment_histories()
    {
        return $this->hasMany(SupplierCreditPaymentHistory::class);
    }

    public function userstoremappers()
    {
        return $this->hasMany(Userstoremapper::class);
    }

    public function activeuserstoremappers()
    {
        return $this->hasMany(Userstoremapper::class)->whereHas('warehousestore', function ($query) {
            $query->where('status', 1);
        });
    }

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
    }



    public function getStores()
    {
         if($this->userstoremappers->count() == 0) return [];

        return $this->userstoremappers->map(function($store){
            return [
                'name' => $store->warehousestore->name
            ];
        })->pluck('name')->toArray();
    }

}
