<?php

namespace Tests\Feature;

use App\PrintSheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RetrievePrintSheetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function print_sheets_can_be_retrieved()
    {

        $this->withoutExceptionHandling();

        $printSheets = factory(PrintSheet::class, 1)->create();

        $response = $this->get('/api/print-sheets/' . $printSheets->first()->id);

        $response->assertStatus(200)->assertJson([
            'data' => [
                'type' => 'print-sheets',
                'print_sheet_id' => $printSheets->first()->id,
                'attributes' => [
                    'type' => 'test',
                    'sheet_url' => $printSheets->first()->sheet_url,
                ],
            ], 'links' => [
                'self' => url('/print-sheets/' . $printSheets->first()->id),

            ],
        ]);
    }
}
