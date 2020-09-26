<?php

namespace Tests\Unit;

use App\PrintSheet;
use PHPUnit\Framework\TestCase;

class PrintSheetTest extends TestCase
{
    public function setUp(): void
    {
        $this->printSheet = new PrintSheet;
    }

    /** @test */
    public function sheet_returns_correct_number_of_rows_and_columns()
    {
        $this->printSheet->createSheet();

        $sheet = $this->printSheet->sheet;

        $rows = $sheet->count();

        $columns = count($sheet->first());

        $this->assertEquals($rows, 15);

        $this->assertEquals($columns, 10);
    }
}
