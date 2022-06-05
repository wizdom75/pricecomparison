<?php

namespace App\Services;

use App\Price;
use App\Product;
use App\ProductCodes;
use App\ProductImage;
use App\ProductImageLink;

trait DeleteOldProductServiceTrait
{
    public function delete()
    {
        foreach (Product::where(['min_price' => 0.00, 'max_price' => 0.00])->get() as $product) {

            if (Price::where('product_id', $product->id)->first()) {
                continue;
            }
            foreach (ProductImage::where('product_id', $product->id)->get() as $image) {
                $image_path = public_path().'/'.$image->path;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }

                $image->delete();

            }

            foreach (ProductImageLink::where('product_id', $product->id)->get() as $link) {
                $link->delete();
            }

            foreach (ProductCodes::where('product_id', $product->id)->get() as $code) {
                $code->delete();
            }

            $product->delete();
        }
    }

}
