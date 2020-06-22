<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccountExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(int $company_id, $journal_id=[])
    {
        $this->company_id = $company_id;
        $this->journal_id = $journal_id;
    }
    public function headings(): array
    {
        return [
            'account_no',
            'account_name',
            'account_type',
            'account_parent_no',
            'opening_balance',
            'opening_balance_date',
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DB::table(DB::raw('accounts a'))
        ->leftJoin(DB::raw('accounts b'), DB::raw('b.id'), '=', DB::raw('a.account_parent_id'))
        ->where('a.company_id', $this->company_id)
        ->selectRaw("a.account_no, a.account_name, a.account_type_id as account_type, b.account_no as account_parent_no, null as opening_balance, null as opening_balance_date")
        ->orderBy('a.id', 'asc')->get();
    }
}
