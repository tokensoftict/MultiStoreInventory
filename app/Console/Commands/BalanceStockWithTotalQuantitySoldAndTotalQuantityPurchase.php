<?php

namespace App\Console\Commands;

use App\Models\Stock;
use Illuminate\Console\Command;

class BalanceStockWithTotalQuantitySoldAndTotalQuantityPurchase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance:stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Stock::with(['stockbatches', 'purchase_order_items', 'invoice_items', 'warehousestore'])->chunk(20, function ($stocks) {
            foreach ($stocks as $stock) {
                $store = $stock->warehousestore->packed_column;

                $totalSold = $stock->invoice_items->sum('qty');
                $totalPurchased = $stock->purchase_order_items->sum('qty');
                $available = $stock->stockbatches->sum($store);
                if(($totalPurchased - $totalSold) != $available){
                    $this->info($stock->name." -> ".($totalPurchased - $totalSold)." - ".$available." -> ".$stock->warehousestore->name);
                }
            }
        });
    }
}
