<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sortir extends Model
{
    protected $fillable = ['name', 'display_name', 'company_id', 'description'];
    public function meta(){
        return $this->hasMany('App\SortirMeta');
    }
    public function data(){
        return $this->hasManyThrough('App\SortirData', 'App\SortirMeta');
    }
}
