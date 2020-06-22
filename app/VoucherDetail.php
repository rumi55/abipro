<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherDetail extends Model
{
    protected $fillable = ['sequence','account_id', 'description', 'debit', 'credit','voucher_id', 'department_id'];
    
    public function voucher(){
        return $this->belongsTo('App\Voucher', 'voucher_id','id');
    }
    public function account(){
        return $this->belongsTo('App\Account', 'account_','id');
    }
    public function department(){
        return $this->belongsTo('App\Department', 'department_id','id');
    }
    public function sortirData($sortir_id){
        $voucherSortir = VoucherSortir::where('sortir_id', $sortir_id)
        ->where('voucher_detail_id',$this->id)
        ->first();
        return $voucherSortir!=null?$voucherSortir->sortirData:null;
    }
}
