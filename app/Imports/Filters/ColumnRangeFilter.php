<?php

namespace App\Imports\Filters;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Loads only columns A..$lastColumn. Filtering at read time (not after) keeps
 * sheets whose formatting runs out to column XFD from exhausting memory.
 */
class ColumnRangeFilter implements IReadFilter
{
    private int $lastIndex;

    public function __construct(string $lastColumn)
    {
        $this->lastIndex = Coordinate::columnIndexFromString($lastColumn);
    }

    public function readCell($columnAddress, $row, $worksheetName = '')
    {
        return Coordinate::columnIndexFromString($columnAddress) <= $this->lastIndex;
    }
}
