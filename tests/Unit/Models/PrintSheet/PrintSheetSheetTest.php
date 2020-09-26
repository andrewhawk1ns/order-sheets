<?php

namespace Tests\Unit;

use App\PrintSheet;
use PHPUnit\Framework\TestCase;

class PrintSheetSheetTest extends TestCase
{
    public function setUp(): void
    {
        $this->printSheetInstance = new PrintSheet;

        $this->sheet = $this->printSheetInstance->createSheet();

    }

    /** @test */
    public function sheet_returns_correct_number_of_rows_and_columns()
    {

        $rows = $this->sheet->count();

        $firstRow = $this->sheet->first();

        $columns = count($firstRow);

        $this->assertEquals($rows, 15);

        $this->assertEquals($columns, 10);
    }
}
