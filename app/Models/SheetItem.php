<?php

namespace App\Models;

class SheetItem
{
    private $width;

    private $height;

    private $x;

    private $y;

    public function __construct($x, $y, $width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->x = $x;
        $this->y = $y;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getWidth()
    {
        return $this->width;
    }

}
