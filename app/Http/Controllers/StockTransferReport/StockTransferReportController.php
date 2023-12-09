<?php

namespace App\Http\Controllers\StockTransferReport;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehousestore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferReportController extends Controller
{
    public function transfer_report(Request $request)
    {
        if($request->get('from') && $request->get('to')){
            $data['from']  = $request->get('from');
            $data['to']  = $request->get('to');
        }else{
            $data['from']  = date('Y-m-01');
            $data['to']  = date('Y-m-t');
        }
        $data['title'] = "Stock Transfer Report";
        $data['transfers'] = StockTransfer::with(['store_to','store_from','user'])
            ->where(function($query){
                $query->orwhere('from', getActiveStore()->id)->orwhere('to', getActiveStore()->id);
            })
            ->whereBetween('transfer_date',[ $data['from'], $data['to']])->orderBy('transfer_date','DESC')->get();
        return view("stock.transfer.report",$data);
    }

    public function product_transfer_report(Request $request)
    {
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['status'] = "COMPLETE";
        $data['title'] = "Stock Transfer Analysis Report For ".getActiveStore()->name;

        $data['datas'] =  StockTransferItem::select('stock_id',
            DB::raw( 'SUM(quantity) as total_qty'),
            DB::raw( 'SUM(quantity * (cost_price)) as total_cost_total'),
            DB::raw( 'SUM(quantity * (selling_price)) as total_selling_price')
        )->whereHas('stock_transfer',function($q) use(&$data){
            $q->whereBetween('transfer_date',[$data['from'],$data['to']])
                ->where("status",$data['status'])
                ->where(function($query){
                    $query->orwhere('from', getActiveStore()->id)->orwhere('to', getActiveStore()->id);
                });
        })->groupBy('stock_id')
            ->get();

        return view('stock.transfer.monthly_product',$data);
    }

    public function transfer_report_by_product(Request $request)
    {
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['status'] = "COMPLETE";
        $data['title'] = "Monthly Stock Transferred By Product";
        $data['product'] = $request->get('product', 1);
        $data['product_name'] = Stock::find($data['product'])->name;
        $data['transfers'] = StockTransferItem::with(['stock_transfer.store_to','stock_transfer.store_from','user', 'stock'])
            ->whereHas('stock_transfer', function($query) use($data){
                $query->where('status', $data['status'])
                    ->where(function($query){
                        $query->orwhere('from', getActiveStore()->id)->orwhere('to', getActiveStore()->id);
                    })
                    ->whereBetween('transfer_date',[$data['from'],$data['to']]);
            })
            ->orderBy('id','DESC')->get();

        return view('stock.transfer.monthly_by_product',$data);
    }
}
