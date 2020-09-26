<?php

namespace Tests\Unit;

use App\PrintSheet;
use Tests\TestCase;

class PrintSheetPlacementTest extends TestCase
{
    public function setUp(): void
    {
        $this->printSheetInstance = new PrintSheet;

        $this->sheet = $this->printSheetInstance->createSheet();
    }

    /** @test */
    public function product_can_be_placed_on_the_grid()
    {

        $placement = $this->printSheetInstance->scanNextAvailable(['width' => 2, 'height' => 5]);

        $this->assertIsArray($placement);
    }

    /** @test */
    public function product_wont_be_placed_if_too_large()
    {
        $placement = $this->printSheetInstance->scanNextAvailable(['width' => 11, 'height' => 16]);
        $this->assertFalse($placement);
    }

    /** @test */
    public function product_is_placed_on_closest_x_axis_grid_unit()
    {
        $this->printSheetInstance->scanNextAvailable(['width' => 1, 'height' => 1]);

        $placement = $this->printSheetInstance->scanNextAvailable(['width' => 5, 'height' => 2]);

        $this->assertEquals([1, 2, 3, 4, 5], $placement['x']);
    }

    /** @test */
    public function product_is_placed_on_closest_y_axis_grid_unit()
    {
        $this->printSheetInstance->scanNextAvailable(['width' => 10, 'height' => 5]);

        $placement = $this->printSheetInstance->scanNextAvailable(['width' => 2, 'height' => 5]);

        $this->assertEquals([5, 6, 7, 8, 9], $placement['y']);

    }

    /** @test */
    public function product_moved_to_next_row_if_space_not_available()
    {
        $this->printSheetInstance->scanNextAvailable(['width' => 7, 'height' => 1]);

        $placement = $this->printSheetInstance->scanNextAvailable(['width' => 5, 'height' => 2]);

        $this->assertEquals([1, 2], $placement['y']);

    }

}
