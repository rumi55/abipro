<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model
{
    protected $fillable = ['user_id', 'user_group_id', 'company_id'];

    public function company(){
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }
    public function user(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    public function userGroup(){
        return $this->belongsTo('App\UserGroup', 'user_group_id', 'id');
    }
}
