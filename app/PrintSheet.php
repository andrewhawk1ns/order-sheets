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

    private $availableIndex;

    public function __construct()
    {
        $this->sheet = collect([]);

        $this->totalAvailable = $this->rows * $this->columns;
    }

    public function getSheetAttribute()
    {
        return $this->sheet;
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
        $sheet = $availableIndex = [];

        for ($row = 0; $row < $this->rows; $row++) {
            for ($column = 0; $column < $this->columns; $column++) {
                $sheet[$row][$column] = null;
                $availableIndex[$row][] = $column;
            }
        }

        $this->sheet = collect($sheet);

        $this->availableIndex = $availableIndex;

        return $this->sheet;
    }

    public function addProduct($product)
    {
        // Todo: Scan next available

    }

    public function scanY($start, $height)
    {
        $yBlock = range($start, $start + $height - 1);

        $keys = array_keys($this->availableIndex);

        $available = false;

        foreach ($yBlock as $row) {
            $available = true;
            if (!in_array($row, $keys)) {
                $available = false;
                break;
            }
        }

        return !$available ?: $yBlock;
    }

    public function scanX($row, $start, $width)
    {
        $xBlock = range($start, $start + $width - 1);

        $available = false;

        foreach ($xBlock as $column) {
            $available = true;

            if (!in_array($column, $this->availableIndex[$row])) {
                $available = false;
                break;
            }
        }

        return !$available ?: $xBlock;
    }

}
