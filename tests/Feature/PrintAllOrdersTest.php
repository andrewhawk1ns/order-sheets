<?php

namespace Tests\Feature;

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

        $response = $this->post('/api/print-sheets', [
            'type' => 'test',
        ]);

        $printSheet = PrintSheet::first();

        $response->assertStatus(201)->assertJson(['data' => [[
            'data' => [
                'type' => 'print-sheets',
                'print_sheet_id' => $printSheet->id,
                'attributes' => [
                    'type' => 'test',
                    'sheet_url' => $printSheet->url,
                ],
            ]],
        ]]);
    }
}
