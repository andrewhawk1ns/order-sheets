<?php

namespace App\Models;

class PrintResult extends Base
{
    protected $sheets;

    protected $orders;

    public function __construct(array $sheet, array $sheetItems)
    {
        $this->sheet = $sheet;
        $this->sheetItems = $sheetItems;

    }

}
