<?php

namespace App;

use App\Customer;
use App\OrderItem;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];
    protected $table = 'orders';

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getTotalSizeAttribute()
    {
        return $this->items->reduce(function ($carry, $item) {
            return $carry + $item->product->sizing->getTotalSize();
        });
    }

}
