<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Department;

class CashflowReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $detail = filter_var($request->detail, FILTER_VALIDATE_BOOLEAN);
        if($detail){
            $data= [
                'journal_id'=>encode($this->journal_id),
                'trans_no'=>$this->trans_no,
                'trans_date'=>$this->trans_date,
                'description'=>$this->description,
                'account_id'=>encode($this->account_id),
                'account_no'=>$this->account_no,
                'account_name'=>$this->account_name,
                'total'=>abs($this->total)
            ];
        }else{
            $data= [
                'account_id'=>encode($this->account_id),
                'account_no'=>$this->account_no,
                'account_name'=>$this->account_name,
                'total'=>abs($this->total)
            ];
        }
        return $data;
    }
}
