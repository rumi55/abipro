<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['trans_no', 'trans_date', 'description', 'total', 'transaction_type_id', 'company_id'];

    public function details(){
        return $this->hasMany('App\VoucherDetail');
    }
    public function transactionType(){
        return $this->belongsTo('App\TransactionType');
    }
    public function numbering(){
        return $this->hasManyThrough('App\numbering', 'App\transaction_type_id');
    }

    public function sortirs(){
        $companySetting = CompanySetting::where('key', '=', 'voucher_sortir')->where('company_id', '=', $this->company_id)->first();
        $sortirIDs = json_decode($companySetting->value );
        return Sortir::whereIn('id', $sortirIDs)->get();
    }
}
