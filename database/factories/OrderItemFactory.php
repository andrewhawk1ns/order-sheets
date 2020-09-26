<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Order;
use App\OrderItem;
use App\Product;
use Faker\Generator as Faker;

$factory->define(OrderItem::class, function (Faker $faker) {
    return [
        'quantity' => $faker->numberBetween(1, 5),
        'refund' => 0,
        'resend_amount' => 0,
        'order_id' => factory(Order::class),
        'product_id' => factory(Product::class),
    ];
});
