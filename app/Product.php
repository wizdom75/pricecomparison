<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Product extends Model
{
   use SearchableTrait;

   protected $fillable = [
        'id',
        'category_id',
        'title',
        'slug',
        'mpn',
        'ean',
        'upc',
        'gtin',
        'isbn',
        'description',
        'min_price',
        'max_price',
        'brand_id',
    ];
   /**
    * Searchable rules.
    *
    * @var array
    */
   protected $searchable = [
       /**
        * Columns and their priority in search results.
        * Columns with higher values are more important.
        * Columns with equal values have equal importance.
        *
        * @var array
        */
       'columns' => [
           'products.title' => 10,
           'products.mpn' => 10,
           'products.ean' => 2,
           'products.upc' => 5,
           'products.isbn' => 5,
           'products.gtin' => 2,
           'products.description' => 7,

       ],

   ];
    public function prices()
    {
       return $this->hasMany(Price::class);
    }

    public function merchant()
    {
        return $this->hasOneThrough('Price', 'Merchant');
    }

    public function images()
    {
       return $this->hasOne(ProductImage::class);
    }

    public function product_codes()
    {
       return $this->hasMany(ProductCode::class);
    }

    public function views()
    {
        return $this->hasMany(View::class);
    }

    public function alerts()
    {
       return $this->hasMany(Alert::class);
    }

    public function category()
    {
       return $this->belongsTo(Category::class);
    }

}
