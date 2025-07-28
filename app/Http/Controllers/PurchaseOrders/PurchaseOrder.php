<?php

namespace App\Http\Controllers\PurchaseOrders;

use App\Classes\Settings;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Cashbook;
use App\Models\PaymentMethod;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\SupplierCreditPaymentHistory;
use App\Models\Warehousestore;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder as Po;
use PDF;

class PurchaseOrder extends Controller
{

    protected $settings;

    public function __construct(Settings $_settings){
        $this->settings = $_settings;
    }


    public final function list_product(Request $request)
    {
        $data['title'] = "List Today's Purchase Orders";
        $date = date('Y-m-d');
        if(isset($request->date)){
            $date = $request->date;
        }
        $data['date'] = $date;

        $data['purchase_orders'] = PurchaseOrderItem::query()
            ->with(['user','purchase_order','purchase_order.created_user'])
            ->whereHas('purchase_order', function ($query) use ($date) {
                $query->where('type', 'PURCHASE')
                    ->where('date_created',$date)
                    ->where('warehousestore_id', getActiveStore()->id);
            })->get();

        return view('purchaseorder.list_product', $data);
    }


    public function index(Request $request){
        $data['title'] = "List Today's Purchase Orders";
        $date = date('Y-m-d');
        if(isset($request->date)){
            $date = $request->date;
        }
        $data['date'] = $date;
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])->where('type', 'PURCHASE')->where('date_created',$date)->orderBy('id','DESC');
        //if(app(\App\Classes\Settings::class)->store()->allow_store_to_share_the_same_product == "0") {
            $data['purchase_orders'] = $data['purchase_orders']->where('warehousestore_id', getActiveStore()->id);
        //}
        $data['purchase_orders'] = $data['purchase_orders']->get();
        return view('purchaseorder.list', $data);
    }

    public function returns(){
        $data['title'] = "List Today's Purchase Returns";
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])->where('type', 'RETURN')->where('date_created',date('Y-m-d'))->orderBy('id','DESC');
        //if(app(\App\Classes\Settings::class)->store()->allow_store_to_share_the_same_product == "0") {
            $data['purchase_orders'] = $data['purchase_orders']->where('warehousestore_id', getActiveStore()->id);
        //}
        $data['purchase_orders'] = $data['purchase_orders']->get();
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
        $data['settings'] = $data['porder']->warehousestore_id == 1 ? $this->settings->store() : $data['porder']->warehousestore;
        $pdf = PDF::loadView("print.print_purchase", $data);
        return $pdf->stream('document.pdf');
    }

    public function show($id){
        $data['title'] = 'View Purchase Order';
        $data['porder'] = Po::with(['supplier','purchase_order_items','user','created_user'])->find($id);
        $data['settings'] = $data['porder']->warehousestore_id == 1 ? $this->settings->store() : $data['porder']->warehousestore;;
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

            if(isset($request->supplier_id)) {
                return redirect()->route('purchaseorders.supplier_and_report')->with('success','Payment has been added successfully!');
            }
            return redirect()->route('purchaseorders.add_payment')->with('success','Payment has been added successfully!');

        }

        $data['title'] = "Add Supplier Payment";
        $data['payments'] = PaymentMethod::where('status',1)->where('id','<>',4)->get();
        $data['banks'] = BankAccount::where('status',1)->get();
        $data['customers'] = Supplier::all();
        $data['supplier_id'] =0;
        $data['amount'] = 0;
        if(isset($request->supplier_id)) {
            $data['supplier_id'] = $request->supplier_id;
        }
        if(isset($request->amount)) {
            $data['amount'] = $request->amount;
        }
        return view('purchaseorder.add_payment',$data);
    }



    public function supplier_and_report()
    {
        $data['title'] = "Suppliers and Payment";
        $data['suppliers'] = Supplier::all();
        return setPageContent('purchaseorder.supplier_and_payment',$data);
    }


    public function showpo_total()
    {}


}
