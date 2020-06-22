<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserGroupResource extends JsonResource
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
            'name'=>$this->name,
            'display_name'=>$this->display_name,
            'description'=>$this->description,
            'company_id'=>encode($this->company_id),
        ];
    }
}
