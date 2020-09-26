<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'title' => $faker->productName,
        'vendor' => $faker->sentence(3),
        'type' => 'regular',
        'size' => $faker->randomElement(['small', 'medium', 'large']),
        'price' => $faker->randomFloat(2, 0, 500),
        'inventory_quantity' => $faker->randomNumber(3),
        'sku' => $faker->ean13,
        'design_url' => $faker->imageUrl(),
        'published_state' => 'active',
    ];
});
