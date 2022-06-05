<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Price extends Model
{
    //use SoftDeletes;

    protected $fillable = [
        'product_id',
        'merchant_id',
        'amount',
        'shipping',
        'product_title',
        'promo_text',
        'buy_link',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function images()
    {
       return $this->hasMany(ProductImage::class, 'product_id');
    }
}
