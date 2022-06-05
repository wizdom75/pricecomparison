<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'id',
        'product_id',
        'path',
        'is_default',
        'is_downloaded',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function price()
    {
        return $this->hasOne(Price::class, 'product_id');
    }
}
