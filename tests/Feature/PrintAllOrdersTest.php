<?php

namespace Tests\Feature;

use App\Order;
use App\OrderItem;
use App\PrintSheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintAllOrdersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function orders_can_be_printed()
    {

        $this->withoutExceptionHandling();

        factory(Order::class, 2)->create(['customer_id' => 1])->each(function ($order) {
            for ($i = 0; $i < 5; $i++) {
                $order->items()->save(factory(OrderItem::class, ['order_id' => $order->id])->make());
            }
        });

        $response = $this->post('/api/print-sheets', [
            'type' => 'test',
        ]);

        $printSheet = PrintSheet::first();

        $response->assertStatus(200)->assertJson(['data' => [[
            'data' => [
                'type' => 'print-sheets',
                'print_sheet_id' => $printSheet->id,
                'attributes' => [
                    'type' => 'test',
                    'sheet_url' => $printSheet->sheet_url,
                ],
            ]],
        ]]);
    }

    /** @test */
    public function cannot_print_if_there_are_no_orders()
    {

        $response = $this->post('/api/print-sheets', [
            'type' => 'test',
        ]);

        $response->assertStatus(404)->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'No Orders Found',
                'detail' => 'No orders available to process to sheets.',
            ],
        ]);
    }
}
