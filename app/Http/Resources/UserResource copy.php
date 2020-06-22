<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class UserResource extends JsonResource
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
            'id'    => encode($this->id),
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'photo' => $this->photo,
            'photo_url' => !empty($this->photo)?asset(url_file($this->photo)):$this->photo,
            'company'  => new CompanyResource($this->activeCompany())
        ];
    }
}
