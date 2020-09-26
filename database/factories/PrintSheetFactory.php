<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\PrintSheet;
use Faker\Generator as Faker;

$factory->define(PrintSheet::class, function (Faker $faker) {
    return [
        'type' => 'test',
        'sheet_url' => 'sheet.pdf',
    ];
});
