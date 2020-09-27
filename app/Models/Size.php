<?php

namespace App\Models;

class Size
{
    private $width;

    private $height;

    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getTotalSize()
    {
        return $this->width * $this->height;
    }

}
