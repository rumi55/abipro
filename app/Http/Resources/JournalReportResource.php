<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JournalReportResource extends JsonResource
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
            'journal_description'=>$this->journal_description,
            'description'=>$this->description,
            'tags'=>$this->tags,
            'debit'=>$this->debit,
            'credit'=>$this->credit,
            'balance'=>abs($this->balance),
            'debit_format'=>currency($this->debit),
            'credit_format'=>currency($this->credit),
            'balance_format'=>currency(abs($this->balance)),
            'created_by'=>$this->created_by
        ];
    }
}
