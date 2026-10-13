<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\QuantityUnit;
use Illuminate\Http\Request;

class QuantityUnitController extends Controller
{
    public function index()
    {
        $data['title'] = "Quantity Units Settings";
        $data['units'] = QuantityUnit::orderBy('level')->get();
        return setPageContent('settings.quantity_unit.list-quantity-unit', $data);
    }

    public function edit($id)
    {
        $data['title'] = "Update Quantity Unit";
        $data['unit'] = QuantityUnit::findOrFail($id);
        return setPageContent('settings.quantity_unit.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate(QuantityUnit::$validate);

        $unit = QuantityUnit::findOrFail($id);
        $data = $request->only(QuantityUnit::$fields);
        $data['name'] = trim($data['name']);

        $unit->update($data);

        return redirect()->route('quantity_unit.index')->with('success', 'Quantity unit updated successfully!');
    }

    public function toggle($id)
    {
        $unit = QuantityUnit::findOrFail($id);

        if ($unit->is_base || $unit->level == 3) {
            return redirect()->route('quantity_unit.index')->with('error', 'The base unit cannot be disabled.');
        }

        $this->toggleState($unit);

        return redirect()->route('quantity_unit.index')->with('success', 'Quantity unit status updated successfully!');
    }
}
