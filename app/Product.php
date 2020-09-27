<?php

namespace App;

use App\Models\Size;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function getSizingAttribute()
    {
        $sizeArr = !empty($this->size) ? explode('x', $this->size) : [0, 0];

        return new Size($sizeArr[0], $sizeArr[1]);
    }
}
