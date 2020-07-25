<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'trans_type','trans_no', 'trans_date', 'contact_id', 'amount', 'numbering_id',
        'transaction_type_id', 'company_id', 'account_id', 'department_id', 'tags',
        'created_by', 'updated_by', 'status', 'description'
    ];

    public function details(){
        return $this->hasMany('App\TransactionDetail');
    }
    public function department(){
        return $this->belongsTo('App\Department');
    }
    public function account(){
        return $this->belongsTo('App\Account');
    }
    public function contact(){
        return $this->belongsTo('App\Contact');
    }
    public function transactionType(){
        return $this->belongsTo('App\TransactionType');
    }
    public function numbering(){
        return $this->hasManyThrough('App\numbering', 'App\transaction_type_id');
    }

    public function journal(){
        return Journal::where('transaction_type_id', $this->transaction_type_id)
        ->where('transaction_id', $this->transaction_id)->first();
    }
    public function tags(){
        $tag_id= explode(',', $this->tags);
        return Tag::whereIn('id', $tag_id)->get();
    }

    public function createdBy(){
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
    public function updatedBy(){
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }
    public function approvedBy(){
        return $this->belongsTo('App\User', 'approved_by', 'id');
    }

}
