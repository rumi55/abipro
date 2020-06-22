<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SortirMetaResource extends JsonResource
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
            'name'=>$this->field_name,
            'display_name'=>$this->field_display_name,
            'data_type'=>$this->field_type
        ];
    }
}
