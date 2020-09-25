<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function orders_can_be_printed()
    {

        $this->withoutExceptionHandling();

        $customer = factory(\App\Customer::class)->create();

        $response = $this->post('/api/print-sheets', [
            'customer_id' => $customer->id,
            'type' => 'test',
        ]);

        $printSheet = \App\PrintSheet::first();

        $response->assertStatus(201)->assertJson(['data' => [[
            'data' => [
                'type' => 'print-sheets',
                'print_sheet_id' => $printSheet->id,
                'attributes' => [
                    'sheet_url' => $printSheet->url,
                    'posted_at' => $posts->last()->created_at->diffForHumans(),
                ],
            ]],
        ]]);
    }
}
