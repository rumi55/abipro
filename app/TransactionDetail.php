<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'sequence', 'description', 'account_id', 'transaction_id', 
        'amount', 'created_by', 'updated_by'
    ];
    public function account(){
        return $this->belongsTo('App\Account', 'account_id', 'id');
    }

}
