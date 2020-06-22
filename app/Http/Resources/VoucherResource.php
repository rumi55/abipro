<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
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
            'trans_date'=>$this->trans_date,
            'trans_no'=>$this->trans_no,
            'description'=>$this->from_or_to,
            'total'=>$this->total,
            'total_format'=>format_number($this->total),
            'total_format_currency'=>currency($this->total),
            'transaction_type'=>new TransactionResource($this->transactionType),
            'details'=>VoucherDetail::collection($this->details)
        ];
    }
}