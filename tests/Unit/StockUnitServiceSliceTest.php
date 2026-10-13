<?php

namespace Tests\Unit;

use App\Services\StockUnitService;
use PHPUnit\Framework\TestCase;

class StockUnitServiceSliceTest extends TestCase
{
    public function test_slice_batches_never_double_uses_stock()
    {
        // FIFO allocation of 234 pieces across two batches (200 + 34)
        $all = [
            1 => ['id' => 1, 'qty' => 200.0, 'from' => 'yard_x'],
            2 => ['id' => 2, 'qty' => 34.0, 'from' => 'yard_x'],
        ];
        // first line took 200 (1 carton), second line takes the remaining 34
        $first = StockUnitService::sliceBatches($all, 0, 200);
        $second = StockUnitService::sliceBatches($all, 200, 34);

        $this->assertEquals([1 => 200.0], array_map(function ($b) { return $b['qty']; }, $first));
        $this->assertEquals([2 => 34.0], array_map(function ($b) { return $b['qty']; }, $second));
    }

    public function test_slice_spans_batches()
    {
        $all = [
            1 => ['id' => 1, 'qty' => 10.0, 'from' => 'c'],
            2 => ['id' => 2, 'qty' => 10.0, 'from' => 'c'],
        ];
        $slice = StockUnitService::sliceBatches($all, 5, 10);
        $this->assertEquals(5.0, $slice[1]['qty']);
        $this->assertEquals(5.0, $slice[2]['qty']);
    }
}
