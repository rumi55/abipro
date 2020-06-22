<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SortirData extends Model
{
    protected $fillable = ['sortir_id', 'sortir_meta_id', 'row', 'value'];
    public function meta(){
        return $this->belongsTo('App\SortirMeta', 'sortir_meta_id', 'id');
    }
}
