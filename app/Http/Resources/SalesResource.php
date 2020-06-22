<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesResource extends JsonResource
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
            'quote_no'=>$this->quote_no,
            'order_no'=>$this->order_no,
            'invoice_no'=>$this->invoice_no,
            'quote_date'=>fdate($this->quote_date),
            'order_date'=>fdate($this->order_date),
            'invoice_date'=>fdate($this->invoice_date),
            'quote_due_date'=>fdate($this->quote_due_date),
            'order_due_date'=>fdate($this->order_due_date),
            'invoice_due_date'=>fdate($this->invoice_due_date),
            'status_quote'=>$this->status_quote,
            'status_order'=>$this->status_order,
            'status_invoice'=>$this->status_invoice,
            'quote_disc'=>$this->quote_disc,
            'quote_tax'=>$this->quote_tax,
            'quote_subtotal'=>$this->quote_subtotal,
            'quote_total'=>$this->quote_total,
            'quote_total_disc'=>$this->quote_total_disc,
            'order_disc'=>$this->order_disc,
            'order_tax'=>$this->order_tax,
            'order_subtotal'=>$this->order_subtotal,
            'order_total'=>$this->order_total,
            'order_total_disc'=>$this->order_total_disc,
            'invoice_disc'=>$this->invoice_disc,
            'invoice_tax'=>$this->invoice_tax,
            'invoice_subtotal'=>$this->invoice_subtotal,
            'invoice_total'=>$this->invoice_total,
            'invoice_total_disc'=>$this->invoice_total_disc,
            'customer'=>new ContactResource($this->customer),
            'salesman'=>new ContactResource($this->salesman),
            'quote_term'=>new TermResource($this->quoteTerm),
            'order_term'=>new TermResource($this->orderTerm),
            'invoice_term'=>new TermResource($this->invoiceTerm),
            'details'=>SalesDetailResource::collection($this->details),
            
            'created_by'=>$this->createdBy!==null?array('id'=>encode($this->createdBy->id), 'name'=>$this->createdBy->name):null,
            'updated_by'=>$this->updatedBy!==null?array('id'=>encode($this->updatedBy->id), 'name'=>$this->updatedBy->name):null,
            'created_at'=>fdate($this->created_at, 'd-m-Y H:i'),
            'updated_at'=>fdate($this->updated_at, 'd-m-Y H:i'),
        ];
    }
}
