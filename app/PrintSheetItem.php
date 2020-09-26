<?php

namespace App;

use App\OrderItem;
use App\PrintSheet;
use Illuminate\Database\Eloquent\Model;

class PrintSheetItem extends Model
{
    protected $guarded = [];

    public function printSheet()
    {
        return $this->belongsTo(PrintSheet::class);
    }
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
