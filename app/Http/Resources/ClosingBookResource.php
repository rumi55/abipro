<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClosingBookResource extends JsonResource
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
            'id'=> encode($this->id),
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date,
            'profit'=>$this->profit,
            'notes'=>$this->notes,
            'status'=>$this->status,
        ];
    }
}
