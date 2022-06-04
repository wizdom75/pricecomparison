<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImageLink extends Model
{
    protected $fillable = [
        'product_id',
        'merchant_id',
        'download_path',
        'is_downloaded',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
