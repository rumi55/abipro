<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    public function account(){
        return $this->hasMany('App\Account', 'id', 'account_type_id');
    }
}
