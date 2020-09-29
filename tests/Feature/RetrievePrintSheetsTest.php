<?php

namespace Tests\Feature;

use App\PrintSheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RetrievePrintSheetsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_retrieve_print_sheets()
    {

        $this->withoutExceptionHandling();

        $printSheets = factory(PrintSheet::class, 2)->create();

        $response = $this->get('/api/print-sheets');

        $response->assertStatus(200)->assertJson([
            'data' => [[
                'data' => [
                    'type' => 'print-sheets',
                    'print_sheet_id' => $printSheets->first()->id,
                    'attributes' => [
                        'type' => 'test',
                        'sheet_url' => $printSheets->first()->sheet_url,
                    ],
                ]],
                [
                    'data' => [
                        'type' => 'print-sheets',
                        'print_sheet_id' => $printSheets->last()->id,
                        'attributes' => [
                            'type' => 'test',
                            'sheet_url' => $printSheets->last()->sheet_url,
                        ],
                    ],
                ],
            ],
            'links' => [
                'self' => url('/print-sheets'),

            ],
        ]);
    }
}
