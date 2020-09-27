<?php

namespace App\Models;

class PrintResult
{
    private $sheets;

    private $ordersProcessed;

    public function __construct($sheets, $ordersProcessed)
    {
        $this->sheets = $sheets;
        $this->ordersProcessed = $ordersProcessed;

    }

    public function getSheets()
    {
        return $this->sheets;
    }

    public function getOrdersProcessed()
    {
        return $this->ordersProcessed;
    }

}
