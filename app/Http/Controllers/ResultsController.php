<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\Brand;
use App\Merchant;
use Illuminate\Support\Facades\DB;

class ResultsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('shopfront.results.index');
    }

    /**
     * API search url
     */
    public function search($q)
    {
        //$result = Product::where('title', 'like', '%'.$q.'%')->with('images')->orderBy('title', 'asc')->paginate(16);
        //$term = explode(' ',$q);
        //$my_query = "SELECT * FROM products WHERE MATCH (`title`,`mpn`,`ean`,`upc`,`gtin`,`isbn`,`description`) AGAINST (? IN NATURAL LANGUAGE MODE)";
        //$result = DB::select($my_query, $term);
        //$result = DB::table('products')->where('title', 'LIKE', '%'.$q.'%')->with('images')->paginate(16);
        $result = Product::search($q, null, true)->with('images')->paginate(16);
        return response($result, 200);
    }
}
