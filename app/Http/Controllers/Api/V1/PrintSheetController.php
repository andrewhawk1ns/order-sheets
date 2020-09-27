<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrintSheet;
use App\Http\Resources\PrintSheetCollection;
use App\Order;
use App\PrintSheet;
use App\Services\PrintSheetService\PrintSheetService;
use Illuminate\Http\Request;

class PrintSheetController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePrintSheet $request, PrintSheetService $printSheetService)
    {
        $orders = Order::with('items', 'items.product');

        $printSheetService->processOrders($orders);

        return new PrintSheetCollection(PrintSheet::all());
    }
}
