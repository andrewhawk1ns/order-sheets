<?php

namespace App\Models;

class SheetItem extends Base
{
    protected $width;

    protected $height;

    protected $x;

    protected $y;

    protected $item;

    public function __construct($item, int $x, int $y, int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->x = $x;
        $this->y = $y;
        $this->item = $item;
    }

    /**
     * Get the sheet item's aspect ratio.
     *
     * @return int
     */
    public function getAspectRatio(): int
    {
        if (!$this->height) {
            throw new Exception(__METHOD__ . ":Dividing by zero.");
        }
        return $this->width / $this->height;
    }

}
