<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $balance = $this->openingBalance();
        $data =  [
            'id'=>encode($this->id),
            'custom_id'=>$this->custom_id,
            'name'=>$this->name,
            'unit'=>new ProductUnitResource($this->unit),
            'category'=>new ProductCategoryResource($this->category),
            'description'=>$this->description,
            'buy_price'=>$this->buy_price,
            'sale_price'=>$this->sale_price,
            'image'=>empty($this->image)?$this->image:asset(url_file($this->image)),
            'is_locked'=>$this->isLocked(),
        ];
        return $data;
    }
    
}
