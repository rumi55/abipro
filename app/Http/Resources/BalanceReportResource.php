<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Department;

class BalanceReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $compare_period = $request->compare_period;
        $compare = $request->compare;
        $cumulative = filter_var($request->cumulative, FILTER_VALIDATE_BOOLEAN);
        // $show_total = filter_var($request->show_total, FILTER_VALIDATE_BOOLEAN);
        // $total_year = filter_var($request->total_year, FILTER_VALIDATE_BOOLEAN);
        $data= [
            'account_id'=>encode($this->id),
            'account_no'=>$this->account_no,
            'account_name'=>$this->account_name,
            'account_group'=>$this->account_group,
            'account_type'=>$this->account_type,
            'account_type_id'=>$this->account_type_id,
            'tree_level'=>$this->tree_level,
        ];
        $count = 0;
        if($compare=='department'){
            if(!empty($request->departments)){
                $exploded = explode(',', $request->departments);
                $count = count($exploded);
            }else{
                $count = Department::count()+2;
            }
        }else if($compare=='budget'){
            $count = 4;
        }else{
            // $count = $compare_period+($show_total && $total_year?3:($show_total || $total_year?2:1));
            $count = ($compare_period+1)*($cumulative?2:1);
        }
        for($i=0;$i<$count;$i++){
            $total = 'total_'.$i;   
            $data[$total]=$this->$total;
        }
        return $data;
    }
}
