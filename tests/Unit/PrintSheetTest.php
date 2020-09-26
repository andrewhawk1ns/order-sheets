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
        $columns = $this->printSheetInstance->scanX(0, 0, 2);

        $this->assertIsArray($columns);
    }

    /** @test */
    public function sheet_returns_y_axis_grid_units_if_available()
    {

        $rows = $this->printSheetInstance->scanY(0, 5);

        $this->assertIsArray($rows);
    }

    /** @test */
    public function sheet_returns_correct_number_of_x_axis_grid_units()
    {

        $columns = $this->printSheetInstance->scanX(0, 0, 2);

        $this->assertCount(2, $columns);
    }

    /** @test */
    public function sheet_returns_correct_number_of_y_axis_grid_units()
    {

        $rows = $this->printSheetInstance->scanY(0, 5);

        $this->assertCount(5, $rows);
    }
}
