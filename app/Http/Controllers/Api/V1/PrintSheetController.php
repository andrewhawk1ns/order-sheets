<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrintSheet;
use App\Http\Resources\PrintSheet as PrintSheetResource;
use App\Http\Resources\PrintSheetCollection;
use App\Order;
use App\PrintSheet;
use App\Services\PrintSheetPDFService\PrintSheetPDFService;
use App\Services\PrintSheetService\PrintSheetService;
use Illuminate\Http\Request;

class PrintSheetController extends Controller
{
    public function index()
    {
        return new PrintSheetCollection(PrintSheet::all());
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePrintSheet $request, PrintSheetService $printSheetService, PrintSheetPDFService $pdfService)
    {
        $orders = Order::with('items', 'items.product', 'items.order');

        $storeResult = $printSheetService->processOrders($orders);

        $printedSheets = $pdfService->processSheets($request->type, $storeResult->sheets);

        return new PrintSheetCollection($printedSheets);
    }
    public function show(PrintSheet $printSheet)
    {
        return new PrintSheetResource($printSheet);
    }
}
