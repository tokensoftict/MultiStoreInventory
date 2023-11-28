<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        $data['title'] = "List Product Category";
        $data['title2'] = "Add Product Category";
        $data['categories'] = ProductCategory::all();
        return setPageContent('settings.category.list-category',$data);
    }


    public function create(){

    }


    public function edit($id){

        $data['title'] = "Update Product Category";
        $data['category'] = ProductCategory::find($id);
        return setPageContent('settings.category.edit',$data);
    }


    public function toggle($id){

        $this->toggleState(ProductCategory::find($id));

        return redirect()->route('category.index')->with('success','Operation Successful');

    }


    public function store(Request $request){

        $request->validate(ProductCategory::$validation);

        ProductCategory::create($request->only(ProductCategory::$fields));

        return redirect()->route('category.index')->with('success','Operation Successful');

    }


    public function update(Request $request, $id){

        $request->validate(ProductCategory::$validation);

        ProductCategory::find($id)->update($request->only(ProductCategory::$fields));
        return redirect()->route('category.index')->with('success','Operation Successful');
    }

}
