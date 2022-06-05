<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class ProductCodes extends Model
{
    protected $fillable = [
        'product_id',
        'title',
        'mpn',
        'ean',
        'upc',
        'gtin',
        'isbn',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
