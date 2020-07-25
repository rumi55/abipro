<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable =['tag', 'group', 'item_id', 'item_name' ,'company_id', 'created_by', 'updated_by'];

    public function isLocked(){
        $exists = TransactionDetail::whereRaw("FIND_IN_SET('$this->id', tags)>0")->exists();
        if($exists){
            return true;
        }
        $exists = JournalDetail::whereRaw("FIND_IN_SET('$this->id', tags)>0")->exists();
        if($exists){
            return true;
        }
        return false;
    }
}
