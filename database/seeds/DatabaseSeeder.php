<?php

use App\Customer;
use App\Order;
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

        factory(Customer::class, 5)->create();

        factory(Order::class, 20)->create(['customer_id' => 1])->each(function ($order) {
            for ($i = 0; $i < rand(1, 20); $i++) {
                $order->items()->save(factory(OrderItem::class, ['order_id' => $order->id])->make());
            }
        });
    }
}
