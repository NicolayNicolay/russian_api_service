<?php

namespace Modules\Orders\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ChunkService implements IReadFilter
{
    private int $_startRow = 0;

    private int $_endRow = 0;

    /**  We expect a list of the rows that we want to read to be passed into the constructor  */
    public function __construct($startRow, $chunkSize) {
        $this->_startRow	= $startRow;
        $this->_endRow		= $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = ''): bool
    {
        //  Only read the heading row, and the rows that were configured in the constructor
        if (($row == 1) || ($row >= $this->_startRow && $row < $this->_endRow)) {
            return true;
        }
        return false;
    }
}
