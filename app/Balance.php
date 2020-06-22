<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $fillable = [
        'account_id', 'balance_date', 'balance_year', 'balance_period', 'balance', 'company_id',
        'credit', 'debit'
    ];

    public function account(){
        return $this->belongsTo('App\Account', 'account_id', 'id');
    }
    
}
