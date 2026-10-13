<?php

namespace Tests\Unit;

use App\Http\Controllers\Settings\QuantityUnitController;
use App\Models\QuantityUnit;
use Illuminate\Http\Request;
use Tests\TestCase;

class QuantityUnitTest extends TestCase
{
    public function test_active_units_retrieval_and_base_unit()
    {
        $active = QuantityUnit::activeUnits();
        $this->assertCount(3, $active);

        $base = QuantityUnit::getBaseUnit();
        $this->assertEquals(3, $base->level);
        $this->assertTrue((bool)$base->is_base);
        $this->assertEquals('Pieces', $base->name);
    }

    public function test_base_unit_cannot_be_disabled()
    {
        $controller = app(QuantityUnitController::class);
        $base = QuantityUnit::getBaseUnit();

        $response = $controller->toggle($base->id);
        $this->assertEquals('The base unit cannot be disabled.', session('error'));

        $fresh = QuantityUnit::find($base->id);
        $this->assertEquals(1, $fresh->status);
    }

    public function test_packaging_unit_can_be_disabled_and_enabled()
    {
        $controller = app(QuantityUnitController::class);
        $unit1 = QuantityUnit::where('level', 1)->first();
        $unit2 = QuantityUnit::where('level', 2)->first();

        // Disable level 2 (Packets)
        $controller->toggle($unit2->id);
        $this->assertEquals(0, QuantityUnit::find($unit2->id)->status);

        $active = QuantityUnit::activeUnits();
        $this->assertCount(2, $active);
        $this->assertEquals([1, 3], $active->pluck('level')->values()->toArray());

        // Disable level 1 (Cartons) as well: both packaging units are now disabled!
        $controller->toggle($unit1->id);
        $this->assertEquals(0, QuantityUnit::find($unit1->id)->status);

        // Active units should now only contain the base unit (Level 3 Pieces)
        $activeOnlyBase = QuantityUnit::activeUnits();
        $this->assertCount(1, $activeOnlyBase);
        $this->assertEquals(3, $activeOnlyBase->first()->level);
        $this->assertTrue(QuantityUnit::isOnlyPiecesEnabled());

        // Re-enable level 1 and level 2
        $controller->toggle($unit1->id);
        $controller->toggle($unit2->id);
        $this->assertFalse(QuantityUnit::isOnlyPiecesEnabled());
        $this->assertEquals(1, QuantityUnit::find($unit1->id)->status);
        $this->assertEquals(1, QuantityUnit::find($unit2->id)->status);
        $this->assertCount(3, QuantityUnit::activeUnits());
    }

    public function test_can_update_unit_name()
    {
        $controller = app(QuantityUnitController::class);
        $unit1 = QuantityUnit::where('level', 1)->first();

        $request = new Request([
            'name' => 'Boxes',
            'description' => 'Large outer carton box',
        ]);

        $controller->update($request, $unit1->id);

        $fresh = QuantityUnit::find($unit1->id);
        $this->assertEquals('Boxes', $fresh->name);
        $this->assertEquals('Large outer carton box', $fresh->description);
        $this->assertEquals('Boxes', QuantityUnit::nameForLevel(1));

        // Revert back to Cartons
        $request = new Request([
            'name' => 'Cartons',
            'description' => 'Top packaging unit (e.g. Cartons, Boxes, Crates)',
        ]);
        $controller->update($request, $unit1->id);
        $this->assertEquals('Cartons', QuantityUnit::nameForLevel(1));
    }
}
