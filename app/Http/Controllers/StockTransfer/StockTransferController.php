<?php

namespace App\Http\Controllers\StockTransfer;

use App\Classes\Settings;
use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use App\Models\Warehousestore;
use Illuminate\Http\Request;
use PDF;

class StockTransferController extends Controller
{
    protected $settings;

    public function __construct(Settings $_settings){
        $this->settings = $_settings;
    }


    public function index(){
        $data['title'] = "List Today's Stock Transfer";
        $data['transfers'] = StockTransfer::with(['store_to','store_from','user'])->where('transfer_date', date('Y-m-d'))->orderBy('id','DESC')->get();
        return view('stock.transfer.index',$data);
    }

    public function add_transfer(Request $request)
    {
        $data['from'] = NULL;
        $data['to'] =  NULL;
        $data['type'] =  NULL;
        if($request->getMethod() == "POST" && !isset($request->stock_id)){
            if($request->from == $request->to)
                return redirect()->route('stocktransfer.add_transfer')->with('error','Sorry, You can transfer stock to the store');
            else
                $data['from'] = $request->from; $data['to'] = $request->to; $data['type'] = $request->type;
        }
        else if($request->getMethod() == "POST" && isset($request->stock_id))
        {
            return StockTransfer::createStockTransfer($request);
        }
        $data['transfer'] = new StockTransfer();
        $data['transfer']->transfer_date = dailyDate();
        $data['title'] = "New Stock Transfer";
        $data['title2'] = "Today's Stock Transfer";
        $data['stores'] = Warehousestore::all();
        return setPageContent("stock.transfer.form",$data);
    }


    public function delete_transfer($id)
    {
        $transfer = StockTransfer::with(['store_to','store_from','user'])->find($id);
        $transfer->delete();
        return redirect()->route('stocktransfer.add_transfer')->with('success','Stock has been deleted successfully!');
    }


    public function show($id)
    {

        $transfer = StockTransfer::findorfail($id);

        $data['title'] = "View Stock Transfer";

        $data['transfer'] = $transfer;

        return setPageContent("stock.transfer.show",$data);
    }



    public function print_afour($transfer_id)
    {
        $transfer = StockTransfer::findorfail($transfer_id);

        $data['transfer'] = $transfer;

        $data['store'] = $this->settings->store();

        $data['logo'] = $this->settings;

        $pdf = PDF::loadView("print.transfer_print",$data);
        return $pdf->stream('document.pdf');
    }


    public function edit_transfer($transfer_id)
    {

        $transfer = StockTransfer::findorfail($transfer_id);

        $data['title'] = "Edit Stock Transfer";

        $data['title2'] = "Today's Stock Transfer";
        $data['transfer'] = $transfer;

        $data['stores'] = Warehousestore::all();

        $data['from'] = $transfer->from;
        $data['to'] =  $transfer->to;
        $data['type'] =  $transfer->type;

        $data['logo'] = $this->settings;

        return setPageContent("stock.transfer.form",$data);
    }


    public function update(Request $request, $id)
    {
        return StockTransfer::updateStockTransfer($request, $id);
    }

    public function complete($id)
    {
        $transfer = StockTransfer::find($id);
        return $transfer->complete();
    }

}
