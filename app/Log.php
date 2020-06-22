<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    
    public function user(){
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
    public function action(){
        return $this->belongsTo('App\Action', 'action_id', 'id');
    }
}
