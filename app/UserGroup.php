<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $fillable = ['name', 'display_name', 'description', 'company_id'];
    
    public function company(){
        return $this->belongsTo('App\Company');
    }
    
}
