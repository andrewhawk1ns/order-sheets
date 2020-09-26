<?php

namespace Tests\Unit;

use App\PrintSheet;
use PHPUnit\Framework\TestCase;

class PrintSheetTest extends TestCase
{
    public function setUp(): void
    {
        $this->printSheetInstance = new PrintSheet;

        $this->sheet = $this->printSheetInstance->createSheet();

        $this->columnBlock = $this->printSheetInstance->scanX(0, 0, 2);

        $this->rowBlock = $this->printSheetInstance->scanY(0, 5);

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

    /** @test */
    public function sheet_returns_x_axis_grid_units_if_available()
    {
        $this->assertIsArray($this->columnBlock);
    }

    /** @test */
    public function sheet_returns_y_axis_grid_units_if_available()
    {

        $this->assertIsArray($this->rowBlock);
    }

    /** @test */
    public function sheet_returns_correct_number_of_x_axis_grid_units()
    {
        $this->assertCount(2, $this->columnBlock);
    }

    /** @test */
    public function sheet_returns_correct_number_of_y_axis_grid_units()
    {
        $this->assertCount(5, $this->rowBlock);
    }

}
