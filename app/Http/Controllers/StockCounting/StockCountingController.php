<?php

namespace App\Http\Controllers\StockCounting;

use App\Exports\StockExport;
use App\Http\Controllers\Controller;
use App\Imports\StockTakingItemImport;
use App\Impots\Stockimports;
use App\Models\Stock;
use App\Models\StockTaking;
use App\Models\Warehousestore;
use Illuminate\Http\Request;
use Excel;

class StockCountingController extends Controller
{

    public function index()
    {
        $data['title'] = 'List Stock Counting';
        $data['countings'] = StockTaking::all();
        return setPageContent('stockcounting.list', $data);
    }


    public function create()
    {
        $data['title'] = 'New Stock Counting';
        $data['stores'] = Warehousestore::all();
        $data['counting'] = new StockTaking();
        return setPageContent('stockcounting.form', $data);
    }


    public function store(Request $request)
    {
        return StockTaking::createStockTaking($request);
    }


    public function show($id)
    {
        $data['title'] = 'View Stock Counting';
        $data['counting'] = StockTaking::with(['user','warehousestore','stock_taking_items'])->findorfail($id);
        return setPageContent('stockcounting.show', $data);
    }


    public function destroy($id)
    {
        $tk =  StockTaking::find($id);
        $tk->delete();
        return redirect()->route('counting.index')->with('success','Stock Counting has been deleted successfully!..');
    }


    public function export_excel($id){

        $tk = StockTaking::findorfail($id);

        return Excel::download(new StockExport, $tk->name.'.xlsx');
    }

    public function import_excel(Request $request, $id)
    {
        $stockTaking = StockTaking::findorfail($id);

        $stockTaking->stock_taking_items()->delete();

        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        Excel::import(new StockTakingItemImport($stockTaking), request()->file('excel_file'));
        return redirect()->route('import_excel',$id)->with('success','Stock Counting Import was completed  successfully!..');
    }

}
