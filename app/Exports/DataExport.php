<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DataExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct($headings, $queryResults)
    {
        $this->headings = $headings;
        $this->queryResults = $queryResults;
    }
    public function headings(): array
    {
        return $this->headings;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->queryResults;
    }
}
