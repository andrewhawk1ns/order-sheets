<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\OrderItem;
use App\PrintSheet;
use App\PrintSheetItem;
use Faker\Generator as Faker;

$factory->define(PrintSheetItem::class, function (Faker $faker) {
    return [
        'status' => 'complete',
        'image_url' => 'image.jpg',
        'size' => $faker->randomNumber(5),
        'x_pos' => $faker->numberBetween(0, 10),
        'y_pos' => $faker->numberBetween(0, 15),
        'width' => $faker->randomNumber(5),
        'height' => $faker->randomNumber(5),
        'identifier' => $faker->sentence(3),
        'print_sheet_id' => factory(PrintSheet::class),
        'order_item_id' => factory(OrderItem::class),
    ];
});
