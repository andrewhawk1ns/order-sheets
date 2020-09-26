<?php

use App\Customer;
use App\OrderItem;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(OrderItem::class, 200)->create();
        factory(Customer::class, 5)->create();
    }
}
