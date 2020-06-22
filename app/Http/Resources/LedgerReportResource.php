<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LedgerReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'journal_id'=>encode($this->journal_id),
            'account_id'=>encode($this->account_id),
            'account_no'=>$this->account_no,
            'account_name'=>$this->account_name,
            'department_name'=>$this->department_name,
            'trans_date'=>fdate($this->trans_date),
            'trans_no'=>$this->trans_no,
            'description'=>$this->description,
            'tags'=>$this->tags,
            'debit'=>$this->debit,
            'credit'=>$this->credit,
            'total_debit'=>$this->total_debit,
            'total_credit'=>$this->total_credit,
            'debit_sign'=>$this->debit_sign,
            'credit_sign'=>$this->credit_sign,
            'opening_balance'=>abs($this->opening_balance),
            'final_balance'=>abs($this->final_balance),
            'debit_format'=>currency($this->debit),
            'credit_format'=>currency($this->credit),
            'opening_balance_format'=>currency($this->opening_balance),
            'final_balance_format'=>currency($this->final_balance),
            'created_by'=>$this->created_by
        ];
    }
}
