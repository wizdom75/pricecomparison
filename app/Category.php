<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'id',
        'title',
        'slug',
        'blurb',
        'parent_id',
        'total_products',
    ];
    /**
     * Self refrencing to create nested categories
     */
    public function parent()
    {
        return $this->belongsTo('App\Category', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Category', 'parent_id', 'id');
    }
    // recursive, loads all descendants
    public function childrenRecursive()
    {
        return $this->children()->with('children');
    }

    // return one level of child items
    public function items()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // recursive relationship
    public function childItems()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('items');
    }
}
