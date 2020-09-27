<?php

namespace App\Services\PrintSheetService;

use App\Models\PrintResult;
use App\Models\Sheet;

class PrintSheetService
{
    private $grid;
    private $sheetSize = 150;
    private $rows = 15;
    private $columns = 10;
    private $orderQueue;
    private $ordersProcessed;

    public function __construct()
    {
        $this->startSheet = new Sheet;
        $this->sheetQueue = [$this->startSheet];
        $this->printSheets = $this->ordersProcessed = [];
        $this->orderQueue = collect([]);
        $this->pollIteration = 0;
    }

    public function processOrders($orders)
    {
        $orders->chunk(10, function ($orderQueue) {
            $this->orderQueue = $orderQueue;
            $this->pollQueue();
        });

        $this->printSheets = array_merge($this->printSheets, $this->sheetQueue);

        return new PrintResult($this->printSheets, $this->ordersProcessed);

    }

    private function pollQueue()
    {
        $this->processQueue();

        $this->pollIteration++;

        if ($this->pollIteration === 110) {
            die();
        }
        if ($this->orderQueue->count() === 0) {
            return;
        } else {
            $this->pollQueue();
        }

    }

    private function addSheetToQueue($elastic = false)
    {
        $newSheet = new Sheet($elastic);

        array_unshift($this->sheetQueue, $newSheet);
    }

    private function removeSheetFromQueue($key)
    {
        unset($$this->sheetQueue[$key]);
        $this->printSheets[] = $sheet;
    }

    public function processQueue()
    {
        $finished = false;

        $this->orderQueue->each(function ($order, $orderKey) use (&$finished) {

            if ($finished) {
                return false;
            }

            foreach ($this->sheetQueue as $sheetKey => $sheet) {

                if ($sheet->getSpaceAvailable() < $order->totalSize || $order->totalSize > $this->sheetSize) {
                    $finished = true;
                    $this->addSheetToQueue(true);
                    break;
                }

                if ($this->processOrder($order, $sheet, $orderKey)) {
                    break;
                } else if ($sheetKey === array_key_last($this->sheetQueue)) {
                    $finished = true;
                    $this->addSheetToQueue(true);
                    break;
                }

                if ($sheet->getSpaceAvailable() === 0) {
                    $finished = true;
                    $this->removeSheetFromQueue($sheetKey);
                    break;
                }

                if (count($this->sheetQueue) > 50) {
                    $finished = true;
                    break;
                }

            }
        });
    }

    public function processOrder($order, $sheet, $orderKey)
    {
        $orderPlaced = true;

        $order->items->each(function ($orderItem, $orderItemKey) use ($sheet, &$orderPlaced) {

            if ($orderItem->product->sizing->getWidth() === 0 || $orderItem->product->sizing->getHeight() === 0) {
                return;
            }

            $placement = $sheet->addToQueue($orderItem->product->sizing->getWidth(), $orderItem->product->sizing->getHeight());

            if (!$placement) {

                $orderPlaced = false;
                return false;
            }

        });

        if ($orderPlaced) {

            $this->ordersProcessed[] = $order;

            $this->orderQueue->forget($orderKey);
            $sheet->commit();
        }

        $sheet->clearQueue();

        return $orderPlaced;
    }
}
