<?php

namespace Tests\Unit;

use App\Models\Deposit;
use App\Models\DepositItem;
use PHPUnit\Framework\TestCase;

class DepositMultiLevelTest extends TestCase
{
    public function test_calculate_deposit_total_with_multi_level_unit()
    {
        $validationReports = [
            '1-yard_quantity-u2' => [
                'batches' => [],
                'prods' => [
                    'id' => 1,
                    'qty' => 100, // 100 pieces
                    'type' => 'yard_quantity',
                    'unit_id' => 2,
                    'unit_name' => 'Carton',
                    'unit_qty' => 2, // 2 cartons
                    'unit_price' => 5000, // 5000 per carton
                    'unit_cost' => 4000,  // 4000 per carton
                    'price' => 100,
                    'cost_price' => 80,
                ],
            ],
            '2-quantity' => [
                'batches' => [],
                'prods' => [
                    'id' => 2,
                    'qty' => 5,
                    'type' => 'quantity',
                    'price' => 1000,
                    'cost_price' => 700,
                ],
            ],
        ];

        $totals = Deposit::calculateDepositTotal($validationReports);

        // 2 cartons @ 5000 = 10,000 + 5 items @ 1000 = 5,000 -> 15,000
        $this->assertEquals(15000.0, $totals['total_deposit_total_selling']);
        // 2 cartons @ 4000 = 8,000 + 5 items @ 700 = 3,500 -> 11,500
        $this->assertEquals(11500.0, $totals['total_deposit_total_cost']);
        // 15000 - 11500 = 3500
        $this->assertEquals(3500.0, $totals['total_deposit_total_profit']);
    }
}
