<?php

namespace App\Models;

use App\Models\Base;

class Size extends Base
{
    protected $width;

    protected $height;

    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Get the total size that would be taken on the sheet.
     *
     * @return int
     */
    public function getTotalSize(): int
    {
        return $this->width * $this->height;
    }

}
