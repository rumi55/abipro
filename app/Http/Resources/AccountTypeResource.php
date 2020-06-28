<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountTypeResource extends JsonResource
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
            'id'=>$this->id,
            'name'=>tt($this,'name'),
            'group'=>$this->group,
            'debit_sign'=>$this->debit_sign,
            'credit_sign'=>$this->credit_sign,
        ];
    }
}
