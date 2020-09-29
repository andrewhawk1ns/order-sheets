<?php

namespace Tests\Unit;

use App\Models\Sheet;
use Tests\TestCase;

class PlaceProductsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->printSheetInstance = new Sheet;
        $this->item = new class

        {};
    }

    /** @test */
    public function product_can_be_placed_on_the_grid()
    {

        $placement = $this->printSheetInstance->addToQueue($this->item, 2, 5);

        $this->assertIsArray($placement);
    }

    /** @test */
    public function product_wont_be_placed_if_too_large()
    {
        $placement = $this->printSheetInstance->addToQueue($this->item, 11, 16);
        $this->assertFalse($placement);
    }

    /** @test */
    public function product_is_placed_on_closest_x_axis_grid_unit()
    {
        $this->printSheetInstance->addToQueue($this->item, 1, 1);

        $placement = $this->printSheetInstance->addToQueue($this->item, 5, 2);

        $this->assertEquals([1, 2, 3, 4, 5], $placement['x']);
    }

    /** @test */
    public function product_is_placed_on_closest_y_axis_grid_unit()
    {
        $this->printSheetInstance->addToQueue($this->item, 10, 5);

        $placement = $this->printSheetInstance->addToQueue($this->item, 2, 5);

        $this->assertEquals([5, 6, 7, 8, 9], $placement['y']);

    }

    /** @test */
    public function product_moved_to_next_row_if_space_not_available()
    {
        $this->printSheetInstance->addToQueue($this->item, 7, 1);

        $placement = $this->printSheetInstance->addToQueue($this->item, 5, 2);

        $this->assertEquals([1, 2], $placement['y']);

    }

}
