<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
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
            'budget_year'=>$this->budget_year,
            'budget_month'=>$this->budget_month,
            'budget'=>$this->budget_total,
            'budget_format'=>format_number($this->budget_total),
            'budget_format_currency'=>currency($this->budget_total),
            'account'=>new AccountResource($this->account),
            'department'=>new DepartmentResource($this->department),
        ];
    }
}
