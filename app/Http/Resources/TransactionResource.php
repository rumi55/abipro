<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
        return [
            'id'=>encode($this->id),
            'trans_no'=>$this->trans_no,
            'trans_date'=>fdate($this->trans_date),
            'amount'=>$this->amount,
            'amount_format'=>format_number($this->amount),
            'amount_format_currency'=>currency($this->amount),
            'department'=>new DepartmentResource($this->department),    
            'tags'=>$this->tags,
            // 'tags_id'=>$tags_id,
            'account'=>new AccountResource($this->account),
            'contact'=>new ContactResource($this->contact),
            'transaction_type'=>new TransactionTypeResource($this->transactionType),
            'details'=>TransactionDetailResource::collection($this->details),
            'journal'=> new JournalResource($this->journal()),
            'created_by'=>$this->createdBy!==null?array('id'=>encode($this->createdBy->id), 'name'=>$this->createdBy->name):null,
            'updated_by'=>$this->updatedBy!==null?array('id'=>encode($this->updatedBy->id), 'name'=>$this->updatedBy->name):null,
            'created_at'=>fdate($this->created_at, 'd-m-Y H:i'),
            'updated_at'=>fdate($this->updated_at, 'd-m-Y H:i'),
        ];
    }
}
