<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $guarded = [];

    public function getSizingAttribute($value)
    {
        return Str::of($this->size)->explode('x');
    }
}
