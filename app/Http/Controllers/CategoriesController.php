<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Product;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = Category::where('parent_id', 0)->with('childrenRecursive')->orderBy('id', 'asc')->get();
        return view('shopfront.categories.index')->with(compact('list'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        // dd(Category::where('parent_id', 0)->with('childItems')->pluck('id'));
        // dd(array_diff(Category::find(4)->childrenRecursive->pluck('id')->toArray(), [0]));
        $category = Category::where('slug', $slug)->first();
        return view('shopfront.categories.show')->with(compact('category'));
    }

}
