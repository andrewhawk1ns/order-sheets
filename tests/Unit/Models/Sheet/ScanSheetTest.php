<?php

namespace Tests\Unit;

use App\Models\Sheet;
use PHPUnit\Framework\TestCase;

class ScanSheetTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->sheetInstance = new Sheet;

        $this->columnBlock = $this->sheetInstance->scanX(0, 0, 2);

        $this->rowBlock = $this->sheetInstance->scanY(0, 0, 5);

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
