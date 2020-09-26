<?php

namespace App;

use App\PrintSheetItem;
use Illuminate\Database\Eloquent\Model;

class PrintSheet extends Model
{
    protected $guarded = [];

    private $rows = 15;

    private $columns = 10;

    private $availableSpace;

    private $sheet;

    public function __construct()
    {
        $this->sheet = collect([]);

        $this->availableSpace = $this->rows * $this->columns;
    }

    public function getSheetAttribute($value)
    {
        return $this->sheet;
    }

    public function items()
    {
        return $this->hasMany(PrintSheetItem::class);
    }

    public function createSheet()
    {
        $sheet = [];

        for ($row = 0; $row < $this->rows; $row++) {
            for ($column = 0; $column < $this->columns; $column++) {
                $sheet[$row][$column] = null;
            }
        }

        $this->sheet = collect($sheet);
    }

}
