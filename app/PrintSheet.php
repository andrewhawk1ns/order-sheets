<?php

namespace App;

use App\PrintSheetItem;
use Illuminate\Database\Eloquent\Model;

class PrintSheet extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(PrintSheetItem::class);
    }

}
