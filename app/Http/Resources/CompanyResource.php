<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $period=$this->getPeriod();
        return [
            'id'=>encode($this->id),
            'name'=>$this->name,
            'company_type_id'=>$this->company_type_id,
            'address'=>$this->address,
            'shipping_address'=>$this->shipping_address,
            'phone'=>$this->phone,
            'fax'=>$this->fax,
            'email'=>$this->email,
            'website'=>$this->website,
            'tax_no'=>$this->tax_no,
            'logo'=>empty($this->logo)?$this->logo:asset(url_file($this->logo)),
            'accounting_start_date'=>fdate($this->accounting_start_date),
            'accounting_period_start'=>fdate($period[0]),
            'accounting_period_end'=>fdate($period[1]),
            'accounting_period_last'=>fdate($period[2]),
        ];
    }
}
