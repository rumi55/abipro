<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JournalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $tag_list = $this->tags();
        // $tags = [];
        // $tags_id = [];
        // foreach($tag_list as $tag){
        //     $tags[]=$tag->tag;
        //     $tags_id[]=encode($tag->id);
        // }

        $data = [
            'id'=>encode($this->id),
            'journal_id'=>$this->journal_id,
            'trans_no'=>$this->trans_no,
            'trans_date'=>fdate($this->trans_date),
            'description'=>$this->description,
            'total'=>$this->total,
            'total_format'=>format_number($this->total),
            'total_format_currency'=>currency($this->total),
            'transaction_id'=>$this->transaction_id,
            'is_locked'=>$this->is_locked,
            'department_id'=>encode($this->department_id),
            'department_name'=>$this->department!=null?$this->department->name:null,
            // 'tags'=>$tags,
            // 'tags_id'=>$tags_id,
            'transaction_type'=>new TransactionTypeResource($this->transactionType),
            'details'=>JournalDetailResource::collection($this->details),
            'created_by'=>$this->createdBy!==null?array('id'=>encode($this->createdBy->id), 'name'=>$this->createdBy->name):null,
            'updated_by'=>$this->updatedBy!==null?array('id'=>encode($this->updatedBy->id), 'name'=>$this->updatedBy->name):null,
            'created_at'=>fdate($this->created_at, 'd-m-Y H:i'),
            'updated_at'=>fdate($this->updated_at, 'd-m-Y H:i'),
        ];
        if(isset($request->include)){
            $fields = explode(',', $request->include);
            $inData = [];
            foreach($fields as $field){
                $newfields = explode(':', $field);
                if(count($newfields)==0){
                    if(array_key_exists($field, $data)){
                        $inData[$field] = $data[$field];
                    }
                }else if(count($newfields)==2){
                    if(array_key_exists($newfields[0], $data)){
                        $inData[$newfields[1]] = $data[$newfields[0]];
                    }
                }
            }
            $data = $inData;
        }
        if(isset($request->exclude)){
            $fields = explode(',', $request->exclude);
            foreach($fields as $field){
                unset($data[$field]);
            }
        }
        return $data;
    }
}