<?php

namespace App\Http\Controllers\StockReport;

use App\Classes\Settings;
use App\Http\Controllers\Controller;
use App\Models\Stockbatch;
use App\Models\StockLogItem;
use App\Models\StockQuantityAdjustment;
use App\Models\Warehousestore;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockReportController extends Controller
{

    public function usage_log_report(Request $request)
    {
        $data['title'] = "Stock Log Report";
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['logs'] = StockLogItem::with(['user','stock','operation','warehousestore', 'stock_log_usages_type'])->whereBetween('log_date',[$data['from'], $data['to']])->where('warehousestore_id', getActiveStore()->id)->get();
        return view("stock.stocklog.stocklog_report",$data);
    }

    public function quantity_adjustment_report(Request $request)
    {
        $data['title'] = "Quantity Adjustment Report";
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['adjustments'] = StockQuantityAdjustment::with('stock')->where('warehousestore_id', getActiveStore()->id)->whereBetween('date_adjusted', [ $data['from'] , $data['to']])->get();
        return view("stock.stocklog.quantity_adjustment_report",$data);
    }


    public function near_out_of_stock(Request $request)
    {
        $data['title'] = "Near Out Of Stock Report";
        $data['warehousestore_id'] = $request->get('warehousestore_id', getActiveStore()->id);
        $data['stores'] = getMyAccessStore('name_and_id');
        $data['store'] = Warehousestore::find($data['warehousestore_id']);

        $sql = "";
        $store = $data['store'];

        $sql .= "SUM(stockbatches." . $store->packed_column . ") as available_quantity,";
        $sql .= "SUM(stockbatches." . $store->yard_column . ") as available_yard_quantity,";

        $sql = rtrim($sql,", ");

        $isMulti = \App\Services\InventoryQuantityService::usesMultiLevel($store);
        $qtyCol = $isMulti ? $store->yard_column : $store->packed_column;

        $stocksQuery = DB::table('stockbatches')->selectRaw(
            "stockbatches.stock_id,stocks.name,$sql, stocks.reorder_level,stocks.selling_price,stocks.cost_price, stocks.type"
            )
            ->join('stocks', 'stockbatches.stock_id', '=', 'stocks.id')
            ->where('stocks.status', 1)
            ->whereNotNull('stocks.reorder_level');

        if ($isMulti) {
            $stocksQuery->whereRaw("(SELECT COALESCE(SUM(sb." . $store->yard_column . "), 0) FROM stockbatches sb WHERE sb.stock_id=stocks.id) <= (stocks.reorder_level * COALESCE((SELECT CASE WHEN pu.units_per_parent > 0 THEN pu.units_per_parent ELSE 1 END FROM product_units pu WHERE pu.stock_id=stocks.id AND pu.level=2 LIMIT 1), 1))");
        } else {
            $stocksQuery->whereRaw("(SELECT COALESCE(SUM(sb." . $store->packed_column . "), 0) FROM stockbatches sb WHERE sb.stock_id=stocks.id) <= stocks.reorder_level");
        }

        $data['nearos'] = $stocksQuery->groupBy('stocks.id','stocks.name')->get();

        return view("stock.stocklog.nearosreport",$data);
    }


    public function stock_expiry_report(Request $request)
    {
        $data['title'] = "Stock Expiry Report";

        $settings = app(Settings::class);
        $nearExpiryDays = (int) ($settings->get('near_expiry_days', 20) ?: 20);
        $data['near_expiry_days'] = $nearExpiryDays;

        $today = Carbon::today();
        $nearExpiryDate = $today->copy()->addDays($nearExpiryDays);

        $packedColumn = getActiveStore()->packed_column;
        $yardColumn = getActiveStore()->yard_column;

        // Expired batches — expiry_date is set, stock has expiry enabled, date has passed, and qty > 0
        $data['expired'] = Stockbatch::with('stock')
            ->whereHas('stock', function ($q) {
                $q->where('expiry', 1)->where('status', 1);
            })
            ->where(function ($q) use ($packedColumn, $yardColumn) {
                $q->where($packedColumn, '>', 0)
                  ->orWhere($yardColumn, '>', 0);
            })
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', $today->toDateString())
            ->orderBy('expiry_date', 'ASC')
            ->get();

        // About-to-expire batches — expiry within the configured window but not yet expired, and qty > 0
        $data['near_expiry'] = Stockbatch::with('stock')
            ->whereHas('stock', function ($q) {
                $q->where('expiry', 1)->where('status', 1);
            })
            ->where(function ($q) use ($packedColumn, $yardColumn) {
                $q->where($packedColumn, '>', 0)
                  ->orWhere($yardColumn, '>', 0);
            })
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', $today->toDateString())
            ->whereDate('expiry_date', '<=', $nearExpiryDate->toDateString())
            ->orderBy('expiry_date', 'ASC')
            ->get();

        return view("stock.stocklog.stock_expiry_report", $data);
    }

}

