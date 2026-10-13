<?php
namespace App\Classes;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\PaymentMethodTable;
use App\Models\QuantityUnit;
use App\Services\InventoryQuantityService;
use App\Services\StockUnitService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard
{

    /**
     * @return int|mixed
     */
    public final function todaysIncome()
    {
        return PaymentMethodTable::query()->where('warehousestore_id', getActiveStore()->id)->where('payment_date', now()->format('Y-m-d'))
            ->whereIn('payment_method_id', [1,2,3,5])
            ->sum('amount');
    }

    /**
     * @return int|mixed
     */
    public final function todaysExpenses()
    {
        return Expense::query()->where('warehousestore_id', getActiveStore()->id)->where('expense_date', now()->format('Y-m-d'))->sum('amount');
    }

    /**
     * @return int|mixed
     */
    public final function currentMonthIncome()
    {
        return PaymentMethodTable::query()->where('warehousestore_id', getActiveStore()->id)->whereBetween('payment_date', [now()->startOfMonth()->format("Y-m-d"), now()->endOfMonth()->format("Y-m-d")])
            ->whereIn('payment_method_id', [1,2,3,5])
            ->sum('amount');
    }

    /**
     * @return int|mixed
     */
    public final function currentMonthExpenses()
    {
        return Expense::query()->where('warehousestore_id', getActiveStore()->id)->whereBetween('expense_date', [now()->startOfMonth()->format("Y-m-d"), now()->endOfMonth()->format("Y-m-d")])->sum('amount');
    }

    function getMonthlyDateRanges()
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $months = [];

        for ($month = 1; $month <= $currentMonth; $month++) {
            $startDate = Carbon::create($currentYear, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();

            $months[] = [
                'month' => $startDate->format('F'), // Full month name
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ];
        }

        return $months;
    }

    /**
     * @return array
     */
    public final function getYearIncomeChartWithExpense() : array
    {
        $months = $this->getMonthlyDateRanges();
        $yearlyReports = [];
        foreach ($months as $month) {

            $yearlyReports[] = [
                "name" => $month['month'],
                "totalIncome" => PaymentMethodTable::query()->where('warehousestore_id', getActiveStore()->id)
                    ->whereIn('payment_method_id', [1,2,3,5])
                    ->whereBetween('payment_date',[$month['start_date'], $month['end_date']])->sum('amount'),
                "totalExpenses" => Expense::query()->where('warehousestore_id', getActiveStore()->id)->whereBetween('expense_date', [$month['start_date'], $month['end_date']])->sum('amount'),
            ];
        }

        return $yearlyReports;
    }

    /**
     * Get stock items that are at or below their reorder level for the active store.
     * Supports both legacy single-level and multi-level quantity systems.
     *
     * @return \Illuminate\Support\Collection
     */
    public final function getNearOutOfStockItems()
    {
        $store = getActiveStore();
        if (!$store) {
            return collect();
        }

        $isMulti = InventoryQuantityService::usesMultiLevel($store);
        $qtyColumn = $isMulti ? $store->yard_column : $store->packed_column;

        $stocks = DB::table('stocks')
            ->selectRaw(
                "stocks.id as stock_id, stocks.name, COALESCE(stocks.reorder_level, 5) as reorder_level, " .
                "COALESCE((SELECT SUM(sb." . $qtyColumn . ") FROM stockbatches sb WHERE sb.stock_id = stocks.id), 0) as available_quantity"
            )
            ->where('stocks.status', 1)
            ->get();

        $results = collect();

        foreach ($stocks as $stock) {
            $baseQty = (float)$stock->available_quantity;
            $reorderLevel = (float)$stock->reorder_level;

            if ($isMulti && StockUnitService::hasUnits($stock->stock_id)) {
                $units = StockUnitService::unitsForUi($stock->stock_id, $baseQty);
                $onlyPieces = QuantityUnit::isOnlyPiecesEnabled();

                if ($onlyPieces) {
                    $topUnit = end($units);
                    $topMult = 1.0;
                } else {
                    $topUnit = count($units) ? $units[0] : null;
                    $topMult = $topUnit ? (float)$topUnit['multiplier'] : 1.0;
                }

                $topUnitName = $topUnit ? $topUnit['name'] : 'Pieces';
                $thresholdPieces = $reorderLevel * $topMult;

                if ($baseQty <= $thresholdPieces) {
                    $stock->is_multi = true;
                    $stock->top_unit_name = $topUnitName;
                    $stock->top_unit_multiplier = $topMult;
                    $stock->threshold_pieces = $thresholdPieces;
                    $stock->breakdown_label = StockUnitService::breakdownLabel($stock->stock_id, $baseQty);
                    $stock->reorder_label = ($reorderLevel + 0) . ' ' . $topUnitName . ($topMult > 1 ? ' (' . ($thresholdPieces + 0) . ' pcs)' : '');
                    $results->push($stock);
                }
            } else {
                if ($baseQty <= $reorderLevel) {
                    $stock->is_multi = false;
                    $stock->breakdown_label = null;
                    $stock->reorder_label = number_format($reorderLevel);
                    $results->push($stock);
                }
            }
        }

        return $results;
    }
}