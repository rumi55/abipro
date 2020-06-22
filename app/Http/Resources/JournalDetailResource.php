<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JournalDetailResource extends JsonResource
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
            'journal_id'=>encode($this->journal_id),
            'sequence'=>$this->sequence,
            'trans_date'=>fdate($this->trans_date),
            'trans_no'=>$this->trans_no,
            'description'=>$this->description,
            'tags'=>$this->tags,
            // 'tags_id'=>$tags_id,
            'debit'=>$this->debit,
            'debit_format'=>format_number($this->debit),
            'debit_format_currency'=>currency($this->debit),
            'credit'=>$this->credit,
            'credit_format'=>format_number($this->credit),
            'credit_format_currency'=>currency($this->credit),
            'department_id'=>encode($this->department_id),
            'department_name'=>$this->department!=null?$this->department->name:null,
            'account_id'=>encode($this->account_id),
            // 'journal'=>new JournalResource($this->journal),
            'account'=>new AccountResource($this->account),
        ];
        return $data;
    }
}
