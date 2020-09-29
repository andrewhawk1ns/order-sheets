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

    private $queueAvailable;

    protected $rows;

    protected $columns;

    protected $store;

    protected $queue;

    protected $pdf;

    protected $elastic = false;

    protected $fresh;

    public function __construct(bool $elastic = false, int $rows = 15, int $columns = 10)
    {
        $this->queue = [];

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

        $this->scanIndex = $scanIndex;

        return $this->sheet;
    }

    public function clearQueue(): void
    {
        $this->queue = $this->store;
        $this->queueSheet = $this->sheet;
        $this->queueAvailable = $this->spaceAvailable;
    }

    public function commit(): void
    {
        $this->sheet = $this->queueSheet;

        $this->spaceAvailable = $this->queueAvailable;

        $this->store = $this->queue;

        $this->fresh = false;

        $this->clearQueue();

    }

    private function addRows(int $height): void
    {
        if (!$height) {
            throw new Exception(__METHOD__ . ":Invalid height provided to add rows ( $height )");
        }
        $rows = range($this->rows, $this->rows + $height - 1);

        $start = $this->rows + $rows[0];
        $this->rows += $height;

        for ($row = $start; $row < $this->rows; $row++) {

            for ($column = 0; $column < $this->columns; $column++) {
                $scanIndex[$row][] = $column;

            }
        }
    }

    private function addColumns(int $width): void
    {
        if (!$width) {
            throw new Exception(__METHOD__ . ":Invalid width provided to add rows ( $width )");
        }
        $columns = range($this->columns, $this->columns + $width - 1);

        $start = $this->columns + $columns[0];
        $this->columns += $width;

        for ($row = 0; $row < $this->rows; $row++) {
            for ($column = $start; $column < $this->columns; $column++) {
                $scanIndex[$row][] = $column;
            }
        }
    }

    private function checkProportion(): int
    {
        return $this->rows < (3 * $this->columns);
    }

    public function addToQueue($item, int $width = 0, int $height = 0)
    {

        $location = $this->scanNextAvailable($width, $height);

        if (!!$location) {

            if ($this->elastic) {
                if ($width > $height && $this->checkProportion()) {
                    $this->rows += $height;
                    $this->addRows($height);
                } else {
                    $this->columns += $width;
                    $this->addColumns($width);
                }
            }

            $this->queueAvailable -= $width * $height;

            $this->removeScanBlock($location['x'], $location['y']);

            $sheetItem = new SheetItem($item, $location['x'][0], $location['y'][0], $width, $height);

            $this->queue[] = $sheetItem;

            foreach ($location['y'] as $row) {
                foreach ($location['x'] as $col) {
                    $this->queueSheet[$row][$col] = $sheetItem;
                }
            }
        }

        return $location;
    }

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
