<?php

namespace App;

use App\PrintSheetItem;
use App\Scopes\ReverseScope;
use Illuminate\Database\Eloquent\Model;

class PrintSheet extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::addGlobalScope(new ReverseScope());
    }
    public function items()
    {
        return $this->hasMany(PrintSheetItem::class);
    }

}
