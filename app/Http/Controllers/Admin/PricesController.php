<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Price;
use App\Product;
use App\ProductCodes as ProductCode;
use App\ProductImageLink;
use App\Brand;

class PricesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(Request()->get('q')){
            $prices = Price::where('merchant_id', 'like', '%'.Request()->get('q').'%')->paginate(10);
        }else{
            $prices = Price::orderBy('created_at', 'DESC')->paginate(10);
        }
        return view('admin.prices.index')->with(compact('prices'));
    }

    public function create()
    {
        return view('admin.prices.create');
    }

    public function edit($id)
    {
        $price = Price::find($id);
        return view('admin.prices.edit')->with('price', $price);
    }

    public function destroy($id)
    {
        Price::find($id)->delete();
        return back()->with('success', 'Delete successful');
    }

    /**
     * Method for displaying a prices csv form
     */
    public function csvform()
    {
        return view('admin.prices.csv-form');
    }

}
