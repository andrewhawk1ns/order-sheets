<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\OrderItem;
use App\Product;
use Faker\Generator as Faker;

$factory->define(OrderItem::class, function (Faker $faker) {
    return [
        'quantity' => $faker->numberBetween(1, 5),
        'refund' => 0,
        'resend_amount' => 0,
        'product_id' => factory(Product::class),
    ];
});
