<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'custom_id'=>$this->custom_id,
            'title'=>$this->title,
            'name'=>$this->name,
            'email'=>$this->email,
            'phone'=>$this->phone,
            'mobile'=>$this->mobile,
            'fax'=>$this->fax,
            'tax_no'=>$this->tax_no,
            'address'=>$this->address,
            'company'=>$this->company,
            'is_customer'=>$this->is_customer,
            'is_supplier'=>$this->is_supplier,
            'is_employee'=>$this->is_employee,
            'is_others'=>$this->is_others,
        ];
    }
}
