<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrialBalanceReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($request->version==2){
            $data =[
                'account_id'=>encode($this->id),
                'account_no'=>$this->account_no,
                'account_name'=>$this->account_name,
                'account_group'=>$this->account_group,
                'debit'=>$this->debit,
                'credit'=>$this->credit,
                'op_debit'=>$this->op_debit,
                'total_debit'=>$this->total_debit,
                'op_credit'=>$this->op_credit,
                'total_credit'=>$this->total_credit,
            ];
        }else{
            $data =[
                'account_id'=>encode($this->id),
                'account_no'=>$this->account_no,
                'account_name'=>$this->account_name,
                'debit'=>$this->debit,
                'credit'=>$this->credit,
                'op_balance'=>$this->op_balance,
                'total_balance'=>$this->total_balance,
            ];
        }
        return $data;
    }
}
