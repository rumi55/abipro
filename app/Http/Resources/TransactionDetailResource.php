<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDetailResource extends JsonResource
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
            'sequence'=>$this->sequence,
            'description'=>$this->description,
            'amount'=>$this->amount,
            'amount_format'=>format_number($this->amount),
            'amount_format_currency'=>currency($this->amount),
            'account_id'=>encode($this->account_id),
            'account'=>new AccountResource($this->account),
        ];
        return $data;
    }
}
