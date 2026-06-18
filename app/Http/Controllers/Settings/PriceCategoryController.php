<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\PriceCategory;
use Illuminate\Http\Request;

class PriceCategoryController extends Controller
{
    public function index()
    {
        $data['title'] = "List Price Category";
        $data['title2'] = "Add Price Category";
        $data['categories'] = PriceCategory::all();
        return setPageContent('settings.price_category.list-price-category', $data);
    }


    public function create()
    {

    }

    public function edit($id)
    {
        $data['title'] = "Update Price Category";
        $data['category'] = PriceCategory::findOrFail($id);
        return setPageContent('settings.price_category.edit', $data);
    }

    public function toggle($id)
    {
        $this->toggleState(PriceCategory::findOrFail($id));
        return redirect()->route('price-category.index')->with('success', 'Operation Successful');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:price_categories,name',
            'price_type' => 'required|in:packed,yard',
            'description' => 'nullable',
            'status' => 'required|boolean'
        ]);

        PriceCategory::create($request->only(['name', 'price_type', 'description', 'status']));

        return redirect()->route('price-category.index')->with('success', 'Operation Successful');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:price_categories,name,' . $id,
            'price_type' => 'required|in:packed,yard',
            'description' => 'nullable',
            'status' => 'required|boolean'
        ]);

        PriceCategory::findOrFail($id)->update($request->only(['name', 'price_type', 'description', 'status']));
        return redirect()->route('price-category.index')->with('success', 'Operation Successful');
    }
}
