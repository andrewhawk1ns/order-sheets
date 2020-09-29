<?php

namespace App\Models;

use App\Models\Base;

class StoreResult extends Base
{
    protected $sheets;

    protected $ordersProcessed;

    public function __construct(array $sheets, array $ordersProcessed)
    {
        $this->sheets = $sheets;
        $this->ordersProcessed = $ordersProcessed;

    }

}
