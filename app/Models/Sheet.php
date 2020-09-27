<?php

namespace App\Models;

use App\Models\SheetItem;

class Sheet
{
    protected $guarded = [];

    protected $spaceAvailable;

    protected $sheet = null;

    private $queueSheet = null;

    private $scanIndex;

    private $queueAvailable;

    private $queue;

    private $rows;

    private $columns;

    private $store;

    private $elastic = false;

    public function __construct($elastic = false, $rows = 15, $columns = 10)
    {
        $this->queue = [];

        $this->rows = $rows;

        $this->columns = $columns;

        $this->elastic = $elastic;

        $this->spaceAvailable = $this->queueAvailable = $rows * $columns;

        if (!$this->sheet) {
            $this->createSheet();
        }
    }

    public function getSheet()
    {
        return $this->sheet;
    }

    public function getSpaceAvailable()
    {
        return $this->spaceAvailable;
    }

    /**
     * Creates a new print sheet grid and available print sheet grid index.
     *
     * @return array
     */
    public function createSheet()
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

    public function clearQueue()
    {
        $this->queue = $this->store;
        $this->queueSheet = $this->sheet;
        $this->queueAvailable = $this->spaceAvailable;
    }

    public function commit()
    {
        $this->sheet = $this->queueSheet;

        $this->spaceAvailable = $this->queueAvailable;

        $this->store = $this->queue;

        $this->clearQueue();

    }

    private function addRows($height)
    {
        $rows = range($this->rows, $this->rows + $height - 1);

        $start = $this->rows + $rows[0];
        $this->rows += $height;

        for ($row = $start; $row < $this->rows; $row++) {

            for ($column = 0; $column < $this->columns; $column++) {
                $scanIndex[$row][] = $column;

            }
        }
    }

    private function addColumns($width)
    {
        $columns = range($this->columns, $this->columns + $width - 1);

        $this->columns += $width;

        $start = $this->rows + $rows[0];

        for ($row = 0; $row < $this->rows; $row++) {
            for ($column = $start; $column < $this->columns; $column++) {
                $scanIndex[$row][] = $column;
            }
        }
    }

    private function checkProportion()
    {
        return $this->rows < (3 * $this->columns);
    }

    public function addToQueue($width = 0, $height = 0, $orderItemId = 1)
    {

        $location = $this->scanNextAvailable($width, $height);

        if (!!$location) {

            if ($this->elastic) {
                if ($width > $height && $this->checkProportion()) {
                    $this->rows += $height;
                } else {
                    $this->columns += $width;
                }
            }

            $this->queueAvailable -= $width * $height;

            $this->removeScanBlock($location['x'], $location['y']);

            $this->queue[$orderItemId] = new SheetItem($location['x'][0], $location['y'][0], $width, $height);

            foreach ($location['y'] as $row) {
                foreach ($location['x'] as $col) {
                    $this->queueSheet[$row][$col] = $orderItemId;
                }
            }
        }

        return $location;
    }

    public function scanNextAvailable($width, $height)
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

    private function removeScanBlock($xBlock, $yBlock)
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

    public function scanY($column, $start, $height)
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

    public function scanX($row, $start, $width)
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
