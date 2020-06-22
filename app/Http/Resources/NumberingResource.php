<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NumberingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $counter = new \App\Counter;
        $counter->counter = 0;
        $numbering = new \App\Numbering;
        $numbering->id = $this->id;
        $numbering->name = $this->name;
        $numbering->format = $this->format;
        $numbering->counter_reset = $this->counter_reset;
        $numbering->counter_digit = $this->counter_digit;
        $counter->numbering($numbering);
        // $counter_reset = array('year'=>'Tahun', 'month'=>'Bulan', 'day'=>'Hari');
        return [
            'id'=>encode($this->id),
            'name'=>$this->name,
            'format'=>$this->format,
            'counter_reset'=>$this->counter_reset,
            'counter_digit'=>$this->counter_digit,
            'counter_start'=>$this->counter_start,
            'last_number'=> $counter->last_number,
            'example'=> $counter->last_number,
            'transaction_type'=> new TransactionTypeResource($this->transactionType),
        ];
    }
}
