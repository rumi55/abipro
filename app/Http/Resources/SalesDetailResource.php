<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesDetailResource extends JsonResource
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
            'product'=>new ProductResource($this->product),
            'quantity'=>$this->quantity,
            'unit_price'=>$this->unit_price,
            'disc'=>$this->disc,
            'tax'=>new TaxResource($this->tax),
            'amount'=>$this->amount,
            'total_price'=>$this->total_price,
            
            'created_by'=>$this->createdBy!==null?array('id'=>encode($this->createdBy->id), 'name'=>$this->createdBy->name):null,
            'updated_by'=>$this->updatedBy!==null?array('id'=>encode($this->updatedBy->id), 'name'=>$this->updatedBy->name):null,
            'created_at'=>fdate($this->created_at, 'd-m-Y H:i'),
            'updated_at'=>fdate($this->updated_at, 'd-m-Y H:i'),
        ];
    }
}
