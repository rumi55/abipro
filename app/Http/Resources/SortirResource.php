<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SortirResource extends JsonResource
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
            'is_active'=>$this->is_active,
            'description'=>empty($this->description)?'-':$this->description,
            'meta'=>SortirMetaResource::collection($this->meta)
        ];
    }
}
