<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'custom_id',
        'company_id',
        'description'
    ];

    public function isLocked(){
        $exists = TransactionDetail::where('department_id', $this->id)->exists();
        if($exists){
            return true;
        }
        $exists = JournalDetail::where('department_id', $this->id)->exists();
        if($exists){
            return true;
        }
        return false;
    }
}
