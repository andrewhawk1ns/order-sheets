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

        $this->printSheetInstance = new Sheet;

        factory(Customer::class)->create();

        factory(Order::class, 30)->create(['customer_id' => 1])->each(function ($order) {
            for ($i = 0; $i < rand(1, 20); $i++) {
                $order->items()->save(factory(OrderItem::class, ['order_id' => $order->id])->make());
            }
        });
    }

    /** @test */
    public function order_can_be_processed()
    {

        $order = Order::first();

        $processed = $this->printSheetServiceInstance->processOrder($order, $this->printSheetInstance, 1);

        $this->assertTrue($processed);
    }

    /** @test */
    public function orders_are_processed_and_added_to_the_print_list()
    {

        $result = $this->printSheetServiceInstance->processOrders(Order::with('items', 'items.product'));

        $this->assertCount(30, $result->ordersProcessed);
    }

}
