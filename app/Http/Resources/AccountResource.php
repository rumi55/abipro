<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $balance = $this->openingBalance();
        $data =  [
            'id'=>encode($this->id),
            'account_no'=>(string)$this->account_no,
            'account_name'=>$this->account_name,
            'account_type'=>new AccountTypeResource($this->accountType),
            'account_parent_id'=>encode($this->account_parent_id),
            'account_parent_no'=>$this->parent!=null?$this->parent->account_no:null,
            'account_parent_name'=>$this->parent!=null?$this->parent->account_name:null,
            // 'opening_balance'=>$balance!=null?$balance->balance:null,
            // 'balance_date'=>$balance!=null?fdate($balance->balance_date):null,
            'debit'=>$this->op_debit,
            'credit'=>$this->op_credit,
            'date'=>$this->op_date,
            'has_children'=>$this->has_children,
            'tree_level'=>$this->tree_level,
            // 'is_locked'=>count($this->transaction())>0?true:false,
            'is_locked'=>$this->isLocked(),
            'is_default'=>$this->is_default,
            'company_id'=>encode($this->company_id),
        ];
        if(count($this->children)>0){
            $data['children'] = AccountResource::collection($this->children);
        }
        return $data;
    }
}
