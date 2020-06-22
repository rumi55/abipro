<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BalanceResource extends JsonResource
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
            'id'=>encode($this->id),
            'account_name'=>$this->account->account_name,
            'account_no'=>$this->account->account_no,
            'account_no'=>$this->account->id,
            'balance'=>$this->balance
        ];
    }
}
