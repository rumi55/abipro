<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherSortir extends Model
{
    protected $fillable = ['sortir_id', 'voucher_detail_id', 'sortir_order', 'sortir_data_id'];

    public function voucherDetail(){
        return $this->belongsTo('App\VoucherDetail', 'voucher_detail_id', 'id');
    }
    public function sortir(){
        return $this->belongsTo('App\Sortir', 'sortir_id', 'id');
    }
    public function sortirData(){
        return $this->belongsTo('App\SortirData', 'sortir_data_id', 'id');
    }
}
