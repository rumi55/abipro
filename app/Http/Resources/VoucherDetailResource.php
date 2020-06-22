<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VoucherDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id'=>encode($this->id),
            'description'=>$this->description,
            'debt'=>$this->debit,
            'debt_format'=>format_number($this->debit),
            'debt_format_currency'=>currency($this->debit),
            'credit'=>$this->credit,
            'credit_format'=>format_number($this->credit),
            'credit_format_currency'=>currency($this->debt),
            'account'=>new AccountResource($this->account),
            'department_name'=>$this->department!=null?$this->department->name:null
        ];
        return $data;
    }
}
