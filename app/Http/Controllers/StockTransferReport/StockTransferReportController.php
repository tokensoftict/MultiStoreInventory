<?php

namespace App\Http\Controllers\StockTransferReport;

use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use App\Models\Warehousestore;
use Illuminate\Http\Request;

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
        $data['stores'] = Warehousestore::all();
        $data['transfers'] = StockTransfer::with(['store_to','store_from','user'])->whereBetween('transfer_date',[ $data['from'], $data['to']])->orderBy('transfer_date','DESC')->get();
        return setPageContent("stock.transfer.report",$data);
    }
}
