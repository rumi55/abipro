<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'name'=>$this->name,
            'description'=>$this->description
        ];
    }
}
