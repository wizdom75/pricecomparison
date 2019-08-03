<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Merchant;
use App\Category;

class RetailersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $retailers = Merchant::orderBy('name', 'asc')->paginate(30);
        $categories = Category::where('is_featured', 1)->take(9)->get();
        return view('shopfront.retailers.index')->with(compact('retailers', 'categories'));
    }

    /**
     * Display the specified resource.
     *
     * @param   $mId
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $retailer = Merchant::where('slug', $slug)->first();
        $categories = Category::where('is_featured', 1)->take(9)->get();

        return view('shopfront.retailers.show')->with(compact('categories', 'retailer'));
    }
}