<?php

namespace App\Http\Controllers\PurchaseOrders;

use App\Classes\Settings;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Cashbook;
use App\Models\CreditPaymentLog;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodTable;
use App\Models\Supplier;
use App\Models\SupplierCreditPaymentHistory;
use App\Models\Warehousestore;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder as Po;
use PDF;

class PurchaseOrder extends Controller
{

    protected $settings;

    public function __construct(Settings $_settings){
        $this->settings = $_settings;
    }


    public function index(){
        $data['title'] = "List Today's Purchase Orders";
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])->where('type', 'PURCHASE')->where('date_created',date('Y-m-d'))->orderBy('id','DESC')->get();
        return view('purchaseorder.list', $data);
    }

    public function returns(){
        $data['title'] = "List Today's Purchase Returns";
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])->where('type', 'RETURN')->where('date_created',date('Y-m-d'))->orderBy('id','DESC')->get();
        return view('purchaseorder.list', $data);
    }


    public function create(){
        $data['title'] = 'New Purchase Order';
        $data['suppliers'] = Supplier::where('status',1)->get();
        $data['porder'] = new Po();
        $data['type'] = 'PURCHASE';
        $data['stores'] = Warehousestore::all();
        return view('purchaseorder.form', $data);
    }


    public function create_returns(){
        $data['title'] = 'New Purchase Return';
        $data['suppliers'] = Supplier::where('status',1)->get();
        $data['porder'] = new Po();
        $data['type'] = 'RETURN';
        $data['stores'] = Warehousestore::all();
        return view('purchaseorder.form', $data);
    }


    public function store(Request $request){

        return Po::createPurchaseOrder($request);
    }


    public function print($id){
        $data['title'] = 'Print Purchase Order';
        $data['porder'] = Po::with(['supplier','purchase_order_items','user','created_user'])->find($id);
        $data['settings'] = $this->settings->store();

        $pdf = PDF::loadView("print.print_purchase", $data);
        return $pdf->stream('document.pdf');
    }

    public function show($id){
        $data['title'] = 'View Purchase Order';
        $data['porder'] = Po::with(['supplier','purchase_order_items','user','created_user'])->find($id);
        $data['settings'] = $this->settings->store();
        return view('purchaseorder.show', $data);
    }

    public function destroy($id){
        $po =  Po::find($id);

        foreach ($po->purchase_order_items()->get() as $item){
            $item->stockbatch()->delete();
        }

        $po->delete();

        return redirect()->route('purchaseorders.index')->with('success','Purchase Order has been deleted successfully!');
    }



    public function edit($id){
        $data['porder'] = Po::with(['supplier','purchase_order_items','user','created_user'])->findorfail($id);
        $data['settings'] = $this->settings->store();
        $data['suppliers'] = Supplier::where('status',1)->get();
        $data['stores'] = Warehousestore::all();
        $data['type'] = $data['porder']->type;
        $data['title'] =  $data['porder'] == "PURCHASE" ? 'Edit Purchase Order' : 'Edit Purchase Return';
        return view('purchaseorder.form', $data);
    }


    public function update(Request $request, $id)
    {
        return Po::updatePurchaseOrder($id,$request);
    }

    public function markAsComplete(Request $request, $id){
        $po = Po::find($id);
        return $po->complete();
    }


    public function add_payment(Request $request){
        if($request->getMethod() == "POST"){

            $sup = SupplierCreditPaymentHistory::create(
                [
                    'user_id' => \auth()->id(),
                    'supplier_id' =>$request->customer_id,
                    'purchase_order_id' => NULL,
                    'payment_method_id' => $request->payment_method,
                    'payment_info' => "",
                    'amount' => $request->amount,
                    'payment_date' =>$request->payment_date,
                ]
            );

            if($request->payment_method == "3" && isset($request->bank)) {
                $cashbookData = [
                    "type" => "Debit",
                    "bank_account_id" => $request->bank,
                    "amount" =>$request->amount,
                    "comment" => "Supplier Payment",
                    "transaction_date" => $request->payment_date,
                    "last_updated" => auth()->id(),
                    "user_id" => auth()->id(),
                    "cashbookable_type" => SupplierCreditPaymentHistory::class,
                    "cashbookable_id" => $sup->id,
                ];

                Cashbook::create($cashbookData);
            };

            return redirect()->route('purchaseorders.add_payment')->with('success','Payment has been added successfully!');

        }

        $data['title'] = "Add Supplier Payment";
        $data['payments'] = PaymentMethod::where('status',1)->where('id','<>',4)->get();
        $data['banks'] = BankAccount::where('status',1)->get();
        $data['customers'] = Supplier::all();
        return setPageContent('purchaseorder.add_payment',$data);
    }


}
