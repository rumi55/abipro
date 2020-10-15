<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Journal extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'journal_id', 'trans_no', 'trans_date', 'description', 'total', 'numbering_id',
        'transaction_id', 'transaction_type_id', 'company_id', 'is_voucher', 'is_single_entry',
        'is_locked', 'status','tags', 'department_id', 'created_by', 'updated_by', 'contact_id',
        'is_processed'
    ];

    public function details(){
        return $this->hasMany('App\JournalDetail');
    }
    public function transactionType(){
        return $this->belongsTo('App\TransactionType');
    }
    public function numbering(){
        return $this->belongsTo('App\Numbering', 'numbering_id', 'id');
    }
    public function contact(){
        return $this->belongsTo('App\Contact');
    }
    public function company(){
        return $this->belongsTo('App\Company', 'company_id', 'id');
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
    public function department(){
        return $this->belongsTo('App\Department', 'department_id', 'id');
    }
    public function tags(){
        $tag_id= explode(',', $this->tags);
        return Tag::whereIn('id', $tag_id)->get();
    }

}
