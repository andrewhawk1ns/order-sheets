<?php
namespace App\Services\PrintSheetService;

use App\Models\Sheet;
use App\Models\StoreResult;
use Exception;
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

    public function processOrders(Builder $orders): StoreResult
    {
        if (!!$orders && $orders->count() > 0) {
            $orders->chunk(10, function ($orderQueue) {
                // echo $orderQueue->count();
                $this->orderQueue = $orderQueue;
                $this->pollQueue();
            });
        } else {
            throw new Exception('Order collection is empty.');
        }

        return new StoreResult(array_merge($this->printSheets, $this->sheetQueue), $this->ordersProcessed);

    }

    private function getTotalSheets(): int
    {
        return count(array_merge($this->printSheets, $this->sheetQueue));
    }

    private function pollQueue()
    {
        $this->processQueue();

        // echo 'total sheets' . PHP_EOL;

        // var_dump($this->getTotalSheets());

        // echo 'sheets in queue' . PHP_EOL;

        // var_dump(count($this->sheetQueue));

        // echo PHP_EOL;

        // echo 'orders processed' . PHP_EOL;

        // echo count($this->ordersProcessed);

        // echo PHP_EOL;

        // echo 'order queue' . PHP_EOL;
        // echo count($this->orderQueue);

        // echo PHP_EOL;

        // echo '---------------';

        // echo PHP_EOL;

        if ($this->orderQueue->count() === 0) {
            // echo 'order queue done';

            $lastSheet = end($this->sheetQueue);

            // var_dump($lastSheet->elastic);
            return;
        } else {
            $this->pollQueue();
        }

    }

    private function addSheetToQueue(bool $elastic = false): void
    {
        $newSheet = new Sheet($elastic);

        array_unshift($this->sheetQueue, $newSheet);
    }

    private function removeSheetFromQueue(int $key, Sheet $sheet): void
    {
        unset($this->sheetQueue[$key]);
        $this->printSheets[] = $sheet;
    }

    public function processQueue(): void
    {
        $finished = false;

        $this->orderQueue->each(function ($order, $orderKey) use (&$finished) {

            if ($finished) {
                // echo 'finished';
                return false;
            }

            foreach ($this->sheetQueue as $sheetKey => $sheet) {

                if ($sheet->spaceAvailable < $order->totalSize || $order->totalSize > $this->sheetSize) {
                    // echo 'no space';
                    // var_dump($sheet->spaceAvailable);
                    // var_dump($order->totalSize);
                    $finished = true;
                    // echo 'fresh';
                    // echo $sheet->fresh || $order->totalSize > $this->sheetSize ?: false;
                    $this->addSheetToQueue($sheet->fresh || $order->totalSize > $this->sheetSize ?: false);
                    break;
                }

                if ($this->processOrder($order, $sheet, $orderKey)) {

                    if ($sheet->elastic === true) {
                        // echo 'sheet is elastic';
                        $this->removeSheetFromQueue($sheetKey, $sheet);
                    }

                    break;
                } else if ($sheetKey === array_key_last($this->sheetQueue)) {
                    // echo 'adding sheet';
                    $finished = true;
                    $this->addSheetToQueue(true);
                    break;
                }

                if ($sheet->spaceAvailable === 0) {
                    // echo 'no space';
                    $finished = true;
                    $this->removeSheetFromQueue($sheetKey, $sheet);
                    break;
                }

                if (count($this->sheetQueue) > 50) {
                    // echo 'sheet queue too big';
                    $finished = true;
                    break;
                }

            }
        });
    }

    public function processOrder($order, Sheet $sheet, int $orderKey): bool
    {
        $orderPlaced = true;

        $order->items->each(function ($orderItem, $orderItemKey) use ($sheet, &$orderPlaced) {

            if ($orderItem->product->sizing->width === 0 || $orderItem->product->sizing->height === 0) {
                return;
            }

            $placement = $sheet->addToQueue($orderItem, $orderItem->product->sizing->width, $orderItem->product->sizing->height);

            if (!$placement) {

                $orderPlaced = false;
                return false;
            }

        });

        if ($orderPlaced) {

            $this->ordersProcessed[] = $order;

            // echo 'count after placing' . PHP_EOL;

            // echo count($this->ordersProcessed);

            // echo PHP_EOL;

            $this->orderQueue->forget($orderKey);
            $sheet->commit();
        }

        $sheet->clearQueue();

        return $orderPlaced;
    }
}
