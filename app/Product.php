<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function getSizingAttribute($value)
    {
        if (empty($this->size)) {
            return null;
        }

        $sizeArr = $this->size->explode('x');

        return ['width' => $sizeArr[0], 'height' => sizeArr[1]];
    }
}
