<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'sequence', 'description', 'account_id', 'transaction_id',
        'amount', 'created_by', 'updated_by', 'department_id'
    ];
    public function account(){
        return $this->belongsTo('App\Account', 'account_id', 'id');
    }

    public function department(){
        return $this->belongsTo('App\Department', 'department_id', 'id');
    }

    public function getTags(){
        $tag_id= explode(',', $this->tags);
        return Tag::whereIn('id', $tag_id)->get();
    }
}
