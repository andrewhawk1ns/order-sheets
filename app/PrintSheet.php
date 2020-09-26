<?php

namespace App;

use App\PrintSheetItem;
use Illuminate\Database\Eloquent\Model;

class PrintSheet extends Model
{
    protected $guarded = [];

    private $rows = 15;

    private $columns = 10;

    private $totalAvailable;

    private $sheet;

    private $scanIndex;

    public function __construct()
    {
        $this->sheet = collect([]);

        $this->totalAvailable = $this->rows * $this->columns;
    }

    public function getSheetAttribute()
    {
        return $this->sheet;
    }

    public function getTotalAvailableAttribute()
    {
        return $this->totalAvailable;
    }

    public function items()
    {
        return $this->hasMany(PrintSheetItem::class);
    }

    /**
     * Creates a new print sheet grid and available print sheet grid index.
     *
     * @return Illuminate\Support\Collection
     */
    public function createSheet()
    {
        $sheet = $scanIndex = [];

        for ($row = 0; $row < $this->rows; $row++) {
            for ($column = 0; $column < $this->columns; $column++) {
                $sheet[$row][$column] = null;
                $scanIndex[$row][] = $column;
            }
        }

        $this->sheet = collect($sheet);

        $this->scanIndex = $this->scanIndex = $scanIndex;

        return $this->sheet;
    }

    public function scanNextAvailable($sizing)
    {

        $placed = false;

        $xBlock = $yBlock = 0;

        if ($sizing['height'] > count($this->scanIndex)) {
            return $placed;
        }

        foreach ($this->scanIndex as $key => $row) {

            if ($sizing['width'] > count($this->scanIndex[$key])) {
                continue;
            }

            foreach ($this->scanIndex[$key] as $column) {

                $yBlock = $this->scanY($column, $key, $sizing['height']);
                $xBlock = $this->scanX($key, $column, $sizing['width']);

                if (!!$yBlock && !!$xBlock) {

                    $placed = true;

                    $this->removeScanBlock($xBlock, $yBlock);

                    $this->totalAvailable -= $sizing['height'] * $sizing['width'];

                    break 2;
                }
            }
        }

        return $placed ? ['x' => $xBlock, 'y' => $yBlock] : false;
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
