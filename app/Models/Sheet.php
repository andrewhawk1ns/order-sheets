<?php

namespace App\Models;

use App\Models\Base;
use App\Models\SheetItem;
use Exception;

class Sheet extends Base
{
    protected $spaceAvailable;

    protected $sheet;

    private $queueSheet;

    private $scanIndex;

    private $sheetIndex;

    private $queueAvailable;

    protected $rows;

    protected $columns;

    protected $store;

    protected $orders;

    protected $queueOrders;

    protected $queue;

    protected $pdf;

    protected $elastic = false;

    protected $fresh;

    public function __construct(bool $elastic = false, int $rows = 15, int $columns = 10)
    {
        $this->queue = $this->queueOrders = $this->orders = [];

        $this->rows = $rows;

        $this->columns = $columns;

        $this->fresh = true;

        $this->elastic = $elastic;

        $this->spaceAvailable = $this->queueAvailable = $rows * $columns;

        if (!$this->sheet) {
            $this->createSheet();
        }
    }

    /**
     * Creates a new print sheet grid and available print sheet grid index.
     *
     * @return array
     */
    public function createSheet(): array
    {
        $sheet = $queueSheet = $scanIndex = [];

        for ($row = 0; $row < $this->rows; $row++) {
            for ($column = 0; $column < $this->columns; $column++) {
                $sheet[$row][$column] = null;
                $scanIndex[$row][] = $column;
            }
        }

        $this->sheet = $this->queueSheet = $sheet;

        $this->scanIndex = $this->sheetIndex = $scanIndex;

        return $this->sheet;
    }

    /**
     * Roll back the pending sheet changes.
     *
     * @return void
     */
    public function clearQueue(): void
    {
        $this->queue = $this->store;
        $this->queueSheet = $this->sheet;
        $this->queueAvailable = $this->spaceAvailable;
        $this->queueOrders = $this->orders;
        $this->scanIndex = $this->sheetIndex;
    }

    /**
     * Commits the pending sheet changes.
     *
     * @return void
     */
    public function commit(): void
    {
        $this->sheet = $this->queueSheet;

        $this->spaceAvailable = $this->queueAvailable;

        $this->store = $this->queue;

        $this->fresh = false;

        $this->orders = $this->queueOrders;

        $this->sheetIndex = $this->scanIndex;

        $this->clearQueue();

    }

    /**
     * Adds rows to the sheet.
     *
     * @param  int $height
     * @return void
     */
    private function addRows(int $height): void
    {

        if (!$height) {
            throw new Exception(__METHOD__ . ":Invalid height provided to add rows ( $height )");
        }

        for ($row = $this->rows; $row < $this->rows + $height; $row++) {

            for ($column = 0; $column < $this->columns; $column++) {
                $this->scanIndex[$row][] = $column;

            }
        }

        $this->rows += $height;
    }

    /**
     * Add columns to the sheet.
     *
     * @param  int $width
     * @return void
     */
    private function addColumns(int $width): void
    {
        if (!$width) {
            throw new Exception(__METHOD__ . ":Invalid width provided to add rows ( $width )");
        }

        $start = $this->columns;

        for ($row = 0; $row < $this->rows; $row++) {
            for ($column = $this->columns; $column < $this->columns + $width; $column++) {
                $this->scanIndex[$row][] = $column;
            }
        }

        $this->columns += $width;
    }

    /**
     * Check if the rows are less than 3 x the columns.
     *
     * @return int
     */
    private function checkProportion(): int
    {
        return $this->rows < (3 * $this->columns);
    }

    /**
     * Add an item to the queue.
     *
     * @param  mixed $item
     * @param  int $width
     * @param  int $height
     * @param  array $location
     * @return array
     */
    private function addQueueItem($item, int $width, int $height, array $location): array
    {

        $this->queueAvailable -= $width * $height;

        $this->removeScanBlock($location['x'], $location['y']);

        $sheetItem = new SheetItem($item, $location['x'][0], $location['y'][0], $width, $height);

        $this->queue[] = $sheetItem;

        foreach ($location['y'] as $row) {
            foreach ($location['x'] as $col) {
                $this->queueSheet[$row][$col] = $sheetItem;
            }
        }

        return $location;
    }

    /**
     * Check if the item fits, if it doesn't and the sheet is expandable, expand the sheet and attempt to add it.
     *
     * @param  mixed $item
     * @param  int $width
     * @param  int $height
     * @param  int $trackingId
     * @return mixed
     */
    public function addToQueue($item, int $width = 0, int $height = 0, int $trackingId = 1)
    {

        $location = $this->scanNextAvailable($width, $height);

        if (!in_array($trackingId, $this->queueOrders)) {
            $this->queueOrders[] = $trackingId;
        }

        if (!!$location) {
            return $this->addQueueItem($item, $width, $height, $location);
        }

        if ($this->elastic) {

            if (($width > $height || $width === $height) && $this->checkProportion()) {
                $this->addRows($height);
            } else {
                $this->addColumns($width);
            }

            $location = $this->scanNextAvailable($width, $height);

            if (!!$location) {
                return $this->addQueueItem($item, $width, $height, $location);
            }
        }

        return false;
    }

    /**
     * Scan for the next available position on the sheet.
     *
     * @param  int $width
     * @param  int $height
     * @return mixed
     */
    public function scanNextAvailable(int $width, int $height)
    {

        $found = false;

        $xBlock = $yBlock = 0;

        if ($height > count($this->scanIndex)) {
            return false;
        }

        foreach ($this->scanIndex as $key => $row) {

            if ($width > count($this->scanIndex[$key])) {
                continue;
            }

            foreach ($this->scanIndex[$key] as $column) {

                $yBlock = $this->scanY($column, $key, $height);
                $xBlock = $this->scanX($key, $column, $width);

                if (!!$yBlock && !!$xBlock) {

                    $found = true;

                    break 2;
                }
            }
        }

        return $found ? ['x' => $xBlock, 'y' => $yBlock] : false;
    }

    /**
     * Remove a block from the scan index as it's no longer available.
     *
     * @param  array $xBlock
     * @param  array $yBlock
     * @return void
     */
    private function removeScanBlock(array $xBlock, array $yBlock): void
    {
        foreach ($yBlock as $row) {
            foreach ($xBlock as $col) {
                unset($this->scanIndex[$row][$col]);

            }

            if (count($this->scanIndex[$row]) === 0) {
                unset($this->scanIndex[$row]);
            }
        }

    }

    /**
     * Scan the rows below the current row to see whether there's enough vertical space.
     *
     * @param  int $column
     * @param  int $start
     * @param  int $height
     * @return mixed
     */
    public function scanY(int $column, int $start, int $height)
    {
        $yBlock = range($start, $start + $height - 1);

        $keys = array_keys($this->scanIndex);

        $available = false;

        foreach ($yBlock as $row) {
            $available = true;
            if (!in_array($row, $keys) || !in_array($column, $this->scanIndex[$row])) {
                $available = false;
                break;
            }
        }

        return $available ? $yBlock : false;
    }

    /**
     * Scan the columns next to the current column to see whether there's enough horizontal space.
     *
     * @param  int $row
     * @param  int $start
     * @param  int $width
     * @return mixed
     */
    public function scanX(int $row, int $start, int $width)
    {
        $xBlock = range($start, $start + $width - 1);

        $available = false;

        foreach ($xBlock as $column) {
            $available = true;

            if (!in_array($column, $this->scanIndex[$row])) {
                $available = false;
                break;
            }
        }

        return $available ? $xBlock : false;
    }

}
