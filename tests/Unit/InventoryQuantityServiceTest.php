<?php

namespace Tests\Unit;

use App\Services\InventoryQuantityService as Q;
use PHPUnit\Framework\TestCase;

class InventoryQuantityServiceTest extends TestCase
{
    /** 1 Carton = 20 Packets, 1 Packet = 10 Pieces */
    private function units(): array
    {
        return [
            ['id' => 1, 'name' => 'Carton', 'level' => 1, 'parent_unit_id' => null, 'units_per_parent' => null],
            ['id' => 2, 'name' => 'Packet', 'level' => 2, 'parent_unit_id' => 1, 'units_per_parent' => 20],
            ['id' => 3, 'name' => 'Piece', 'level' => 3, 'parent_unit_id' => 2, 'units_per_parent' => 10],
        ];
    }

    public function test_multipliers()
    {
        $this->assertEquals(200, Q::multiplier($this->units(), 1));
        $this->assertEquals(10, Q::multiplier($this->units(), 2));
        $this->assertEquals(1, Q::multiplier($this->units(), 3));
    }

    public function test_receive_10c_5p_7pc_is_2057()
    {
        $base = Q::toBaseMixed($this->units(), [1 => 10, 2 => 5, 3 => 7]);
        $this->assertEquals(2057, $base);
    }

    public function test_sell_1c_3p_4pc_is_234()
    {
        $this->assertEquals(234, Q::toBaseMixed($this->units(), [1 => 1, 2 => 3, 3 => 4]));
    }

    public function test_single_unit_sales()
    {
        $this->assertEquals(7, Q::toBase($this->units(), 3, 7));
        $this->assertEquals(50, Q::toBase($this->units(), 2, 5));
        $this->assertEquals(400, Q::toBase($this->units(), 1, 2));
        $this->assertEquals(0, Q::toBase($this->units(), 1, 0));
    }

    public function test_breakdown_450()
    {
        $rows = Q::breakdown($this->units(), 450);
        $this->assertEquals([2, 5, 0], array_column($rows, 'quantity'));
        $this->assertEquals('2 Carton, 5 Packet, 0 Piece', Q::breakdownLabel($this->units(), 450));
    }

    public function test_breakdown_roundtrip_2057()
    {
        $rows = Q::breakdown($this->units(), 2057);
        $this->assertEquals([10, 5, 7], array_column($rows, 'quantity'));
    }

    public function test_negative_quantity_rejected()
    {
        $this->expectException(\InvalidArgumentException::class);
        Q::toBase($this->units(), 1, -1);
    }

    public function test_valid_config_has_no_errors()
    {
        $this->assertSame([], Q::validateUnits($this->units()));
    }

    public function test_zero_conversion_invalid()
    {
        $u = $this->units();
        $u[1]['units_per_parent'] = 0;
        $this->assertNotEmpty(Q::validateUnits($u));
    }

    public function test_self_parent_invalid()
    {
        $u = $this->units();
        $u[1]['parent_unit_id'] = 2;
        $this->assertNotEmpty(Q::validateUnits($u));
    }

    public function test_circular_invalid()
    {
        $u = $this->units();
        $u[0]['parent_unit_id'] = 3; // carton under piece, no root left
        $this->assertNotEmpty(Q::validateUnits($u));
    }

    public function test_smallest_must_be_piece()
    {
        $u = array_slice($this->units(), 0, 2);
        $this->assertNotEmpty(Q::validateUnits($u));
    }

    public function test_two_level_packet_piece_is_valid()
    {
        $u = [
            ['id' => 2, 'name' => 'Packet', 'level' => 2, 'parent_unit_id' => null, 'units_per_parent' => null],
            ['id' => 3, 'name' => 'Piece', 'level' => 3, 'parent_unit_id' => 2, 'units_per_parent' => 12],
        ];
        $this->assertSame([], Q::validateUnits($u));
        $this->assertEquals(24, Q::toBase($u, 2, 2));
    }
}
