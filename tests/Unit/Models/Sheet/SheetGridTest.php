<?php

namespace Tests\Unit;

use App\Models\Sheet;
use PHPUnit\Framework\TestCase;

class PrintSheetSheetTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->sheetInstance = new Sheet;

        $this->sheet = $this->sheetInstance->createSheet();

        $this->printSheetInstance = new Sheet;
    }

    /** @test */
    public function sheet_returns_correct_number_of_rows_and_columns()
    {

        $rows = count($this->sheet);

        $columns = count($this->sheet[0]);

        $this->assertEquals($rows, 15);

        $this->assertEquals($columns, 10);
    }
}
