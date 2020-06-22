<?php

namespace App\Exports;

use App\VwLedger;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JournalExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(int $company_id, $journal_id=[])
    {
        $this->company_id = $company_id;
        $this->journal_id = $journal_id;
    }
    public function headings(): array
    {
        return [
            'transaction_date',
            'transaction_no',
            'sequence',
            'account_no',
            'description',
            'debit',
            'credit',
            'total',
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return VwLedger::where('company_id', $this->company_id)
        ->select(['trans_date', 'trans_no', 'sequence', 'account_no','description', 'debit', 'credit', 'total'])
        ->orderBy('trans_date', 'asc')->orderBy('trans_no', 'asc')
        ->limit(100)->get();
    }
}
