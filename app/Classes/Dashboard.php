<?php
namespace App\Classes;

use App\Models\Expense;
use App\Models\Payment;
use Carbon\Carbon;

class Dashboard
{

    /**
     * @return int|mixed
     */
    public final function todaysIncome()
    {
        return Payment::query()->where('warehousestore_id', getActiveStore()->id)->where('payment_date', now()->format('Y-m-d'))->sum('total_paid');
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
        return Payment::query()->where('warehousestore_id', getActiveStore()->id)->whereBetween('payment_date', [now()->startOfMonth()->format("Y-m-d"), now()->endOfMonth()->format("Y-m-d")])->sum('total_paid');
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
                "totalIncome" => Payment::query()->where('warehousestore_id', getActiveStore()->id)->whereBetween('payment_date',[$month['start_date'], $month['end_date']])->sum('total_paid'),
                "totalExpenses" => Expense::query()->where('warehousestore_id', getActiveStore()->id)->whereBetween('expense_date', [$month['start_date'], $month['end_date']])->sum('amount'),
            ];
        }

        return $yearlyReports;
    }
}