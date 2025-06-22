<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\Warehousestore;
use Illuminate\Console\Command;

class FixWrongStockQuantityLocation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-initial-stock-error';

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
        $fromStore = Warehousestore::find(1);
        $toStore = Warehousestore::find(2);
        $num = 0;
        Stock::with(['stockbatches'])->where('warehousestore_id', $toStore->id)->chunk(100, function($stock) use ($fromStore, $toStore, &$num) {
            foreach ($stock as $stock) {
                foreach ($stock->stockbatches as $batch) {
                    $batch->{$toStore->packed_column} = $batch->{$fromStore->packed_column};
                    $batch->{$fromStore->packed_column} = 0;
                    $batch->update();
                    $num++;
                }
            }
        });

        $this->info($num." was updated successfully");
    }
}
