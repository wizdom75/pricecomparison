<?php

namespace App\Services;

use App\Price;
use Illuminate\Support\Carbon;

trait DeleteOldPriceServiceTrait
{
    public function delete()
    {
        foreach (Price::where( 'created_at', '<', Carbon::now()->subDays(30))->get() as $price) {
            $merchant_name = $price->merchant->name;
            $product_title = $price->product->title;
            $last_updated = $price->updated_at;
            echo   "Old price belonging to ($merchant_name) for product ($product_title), last updated ($last_updated) was successfully deleted.\n";
        }
    }
}
