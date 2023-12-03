<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\Stockbatch;
use Illuminate\Console\Command;

class CreateStockBatchRecordForAllStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:create-batch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Stock batch for all stock';

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
        Stock::where('status', 1)->chunk(20, function($stocks){
           foreach ($stocks as $stock){
               Stockbatch::create(
                   [
                       'supplier_id' => NULL,
                       'stock_id' => $stock->id,
                       'expiry_date' => NULL,
                       'quantity' => 0,
                       'yard_quantity' => 0,
                       'received_date' => date('Y-m-d'),
                   ]
               );
           }
        });
        return Command::SUCCESS;
    }
}
