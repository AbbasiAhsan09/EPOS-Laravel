<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cat = ProductCategory::orderBy('id','DESC')->get();
        return view('product_category.index',compact('cat'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $category = new ProductCategory();
        $category->category = $request->category;
        // $category->description = $request->description;
        $category->save();
        toast('Added New Category','success');

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request, ProductCategory $productCategory)
    {
        $category =  ProductCategory::find($id);
        $category->category = $request->category;
        // $category->description = $request->description;
        $category->save();
        toast('Updated Category','info');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id,ProductCategory $productCategory)
    {
       try {
        $cat = ProductCategory::find($id);
        if ($cat) {
            $cat->delete();
            toast("Deleted Category $cat->category!",'error');
        } else {
            toast("Category Not Found",'error');
        }
        
        
        return redirect()->back();
       } catch (\Throwable $th) {
        throw $th;
       }
    }
}
