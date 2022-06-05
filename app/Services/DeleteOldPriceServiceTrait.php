<?php

namespace App\Services;

use App\Price;
use Illuminate\Support\Carbon;

trait DeleteOldPriceServiceTrait
{
    public function delete()
    {
        if ($price = Price::where( 'created_at', '<', Carbon::now()->subDays(30))) {
            $price->delete();
        }
    }
}
