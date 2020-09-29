<?php

namespace Tests\Unit;

use App\Customer;
use App\Models\Sheet;
use App\Order;
use App\OrderItem;
use App\Services\PrintSheetService\PrintSheetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessOrdersTest extends TestCase
{

    use RefreshDatabase;
    public function setUp(): void
    {

        parent::setUp();

        $this->printSheetServiceInstance = new PrintSheetService;

        $this->printSheetInstance = new Sheet(true);

        factory(Customer::class)->create();

    }

    /** @test */
    public function order_can_be_processed()
    {

        factory(Order::class, 1)->create(['customer_id' => 1]);

        factory(OrderItem::class, 1)->create(['order_id' => 1]);

        $order = Order::first();

        $processed = $this->printSheetServiceInstance->processOrder($order, $this->printSheetInstance, 1);

        $this->assertTrue($processed);

    }

    /** @test */
    public function orders_are_processed_and_added_to_the_print_list()
    {

        factory(Order::class, 1)->create(['customer_id' => 1]);

        factory(OrderItem::class, 1)->create(['order_id' => 1]);

        $result = $this->printSheetServiceInstance->processOrders(Order::with('items', 'items.product'));

        $this->assertCount(1, $result->ordersProcessed);
    }

    /** @test */
    public function large_orders_can_be_added_to_a_sheet()
    {

        factory(Order::class, 1)->create(['customer_id' => 1])->each(function ($order) {
            for ($i = 0; $i < 60; $i++) {
                $order->items()->save(factory(OrderItem::class, ['order_id' => $order->id])->make());
            }
        });

        $result = $this->printSheetServiceInstance->processOrders(Order::with('items', 'items.product'));
        $this->assertCount(1, $result->ordersProcessed);
    }

    /** @test */
    public function orders_are_not_split_between_sheets()
    {

        factory(Order::class, 40)->create(['customer_id' => 1])->each(function ($order) {
            for ($i = 0; $i < 5; $i++) {
                $order->items()->save(factory(OrderItem::class, ['order_id' => $order->id])->make());
            }
        });

        $result = $this->printSheetServiceInstance->processOrders(Order::with('items', 'items.product'));

        $orders = [];
        foreach ($result->sheets as $sheet) {
            array_merge($orders, $sheet->orders);
        }

        $this->assertFalse(count($orders) > count(array_unique($orders)));

    }

    /** @test */
    public function orders_can_be_fitted_into_one_sheet()
    {

        factory(Order::class, 2)->create(['customer_id' => 1])->each(function ($order) {
            for ($i = 0; $i < 5; $i++) {
                $order->items()->save(factory(OrderItem::class, ['order_id' => $order->id])->make());
            }
        });

        $result = $this->printSheetServiceInstance->processOrders(Order::with('items', 'items.product'));

        $this->assertCount(1, $result->sheets);

    }

    /** @test */
    public function multiple_sheets_are_created_if_the_orders_are_large()
    {

        factory(Order::class, 3)->create(['customer_id' => 1])->each(function ($order) {
            for ($i = 0; $i < 30; $i++) {
                $order->items()->save(factory(OrderItem::class, ['order_id' => $order->id])->make());
            }
        });

        $result = $this->printSheetServiceInstance->processOrders(Order::with('items', 'items.product'));

        $this->assertTrue(count($result->sheets) > 1);

    }

    /** @test */
    public function does_not_exceed_the_max_number_of_sheets()
    {

        factory(Order::class, 500)->create(['customer_id' => 1])->each(function ($order) {
            for ($i = 0; $i < 30; $i++) {
                $order->items()->save(factory(OrderItem::class, ['order_id' => $order->id])->make());
            }
        });

        $result = $this->printSheetServiceInstance->processOrders(Order::with('items', 'items.product'));

        $this->assertCount(50, $result->sheets);

    }

}
