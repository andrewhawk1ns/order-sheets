<?php
namespace App\Services\PrintSheetService;

use App\Exceptions\OrdersNotFoundException;
use App\Models\Sheet;
use App\Models\StoreResult;
use Illuminate\Database\Eloquent\Builder;

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

    /**
     * Process the orders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $orders
     * @return \App\Models\StoreResult
     */
    public function processOrders(Builder $orders): StoreResult
    {
        if (!!$orders && $orders->count() > 0) {
            $orders->chunk(10, function ($orderQueue) {
                $this->orderQueue = $orderQueue;
                $this->pollQueue();
            });
        } else {
            throw new OrdersNotFoundException();
        }

        $sheets = array_merge($this->printSheets, $this->sheetQueue);

        return new StoreResult($sheets, $this->ordersProcessed);

    }

    /**
     * Get the count of the sheets currently in queue and the sheets that are completely filled.
     *
     * @return int
     */
    private function getTotalSheets(): int
    {
        return count(array_merge($this->printSheets, $this->sheetQueue));
    }

    /**
     * Check the queue for more orders to process.
     *
     * @return void
     */
    private function pollQueue()
    {
        $this->processQueue();

        $this->pollIteration++;

        if ($this->orderQueue->count() === 0 || count($this->sheetQueue) >= 50 || $this->pollIteration > 500) {
            return;
        } else {
            $this->pollQueue();
        }

    }

    /**
     * Add a sheet to the top of the sheet queue.
     *
     * @param bool $elastic
     * @return void
     */
    private function addSheetToQueue(bool $elastic = false): void
    {

        if (count($this->sheetQueue) === 50) {
            return;
        }

        $newSheet = new Sheet($elastic);

        array_unshift($this->sheetQueue, $newSheet);
    }

    /**
     * Add a sheet to the top of the sheet queue.
     *
     * @param int $key
     * @param App\Models\Sheet $sheet
     * @return void
     */
    private function removeSheetFromQueue(int $key, Sheet $sheet): void
    {
        unset($this->sheetQueue[$key]);
        $this->printSheets[] = $sheet;
    }

    /**
     * Process the current sheet queue (available sheets) and attempt to add orders to them.
     *
     * @return void
     */
    public function processQueue(): void
    {
        $finished = false;

        $this->orderQueue->each(function ($order, $orderKey) use (&$finished) {

            if ($finished) {
                return false;
            }

            foreach ($this->sheetQueue as $sheetKey => $sheet) {

                if (($sheet->spaceAvailable < $order->totalSize || $order->totalSize > $this->sheetSize) && !($sheet->fresh && $sheet->elastic)) {

                    if ($sheet->fresh) {
                        $sheet->elastic = true;
                    } else {
                        $this->addSheetToQueue($sheet->fresh || $order->totalSize > $this->sheetSize ?: false);

                        break;
                    }
                }

                if ($this->processOrder($order, $sheet, $orderKey)) {

                    if ($sheet->elastic === true) {
                        // Make the sheet no longer expandable so more orders can be added without the sheet growing.
                        $sheet->elastic = false;
                    }

                    break;

                } else if ($sheetKey === array_key_last($this->sheetQueue)) {
                    // If queue is at the last sheet, add a new elastic sheet to the top which will expand if needed and restart.
                    $finished = true;
                    $this->addSheetToQueue(true);
                    break;
                }

                if ($sheet->spaceAvailable === 0) {
                    // Sheet has no space available, remove it from the queue and restart.
                    $finished = true;
                    $this->removeSheetFromQueue($sheetKey, $sheet);
                    break;
                }

                if (count($this->sheetQueue) > 50) {
                    // Running over 50 sheets, end the processing.
                    $finished = true;
                    break;
                }

            }
        });

    }

    /**
     * Attempt to place the order on the sheet, if it is placed, remove the order from the queue and update the sheet, if not, roll back the changes on the sheet.
     *
     * @param mixed $order
     * @param App\Models\Sheet $sheet
     * @param int $orderKey
     * @return bool $orderPlaced
     */
    public function processOrder($order, Sheet $sheet, int $orderKey): bool
    {

        $orderPlaced = true;

        $order->items->each(function ($orderItem, $orderItemKey) use ($sheet, &$orderPlaced, $order) {
            if ($orderItem->product->sizing->width === 0 || $orderItem->product->sizing->height === 0) {
                return;
            }

            $placement = $sheet->addToQueue($orderItem, $orderItem->product->sizing->width, $orderItem->product->sizing->height, $order->id);
            if (!$placement) {
                $orderPlaced = false;
                return false;
            }

        });

        // If the order is placed, commit the changes to the sheet.
        if ($orderPlaced) {

            $this->ordersProcessed[] = $order;

            $this->orderQueue->forget($orderKey);
            $sheet->commit();
        }

        // If the order is not placed, clear the pending changes on the sheet.
        $sheet->clearQueue();

        return $orderPlaced;
    }
}
