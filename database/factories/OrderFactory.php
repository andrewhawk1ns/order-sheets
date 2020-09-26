<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Order;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'total_price' => $faker->randomFloat(2, 0, 1000),
        'fulfillment_status' => 'fulfilled',
        'fulfilled_date' => $faker->dateTime(),
        'order_status' => $faker->randomElement(['active', 'done']),
        'customer_order_count' => $faker->randomDigitNot(0),
        'order_number' => $faker->randomNumber(8),
        'customer_id' => 1,
    ];
});
