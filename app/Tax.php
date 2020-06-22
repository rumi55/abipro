<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = ['name', 'percentage', 'buy_account_id', 'sales_account_id', 'created_by', 'company_id'];

    public function buyAccount(){
        return $this->belongsTo('App\Account', 'buy_account_id', 'id');
    }
    public function salesAccount(){
        return $this->belongsTo('App\Account', 'sales_account_id', 'id');
    }
}
