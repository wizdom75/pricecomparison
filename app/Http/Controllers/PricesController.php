<?php

namespace App\Http\Controllers;

use App\Price;
use App\Product;
use App\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PricesController extends Controller
{
    private $prod_id;
    /**
     * Display the specified resource.
     *
     * @param  int  $prod_id
     * @return \Illuminate\Http\Response
     */
    public function api_show($prod_id)
    {
        $this->prod_id = $prod_id;

       return Merchant::join('prices', function ($join) {
                    $join->on('merchants.id', '=','prices.merchant_id' )
                        ->where('product_id', $this->prod_id);
                })
                ->orderBy(DB::raw("`amount` + `shipping`"), 'asc')
                ->get();
    }

}
