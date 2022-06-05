<?php

namespace App\Services;

use App\Product;
use App\ProductCodes;

trait AddProductServiceTrait
{
    public function createProduct(array $product_data, $product_code_data)
    {
        Product::create($product_data);
        ProductCodes::create($product_code_data);
    }
}
