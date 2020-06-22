<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SortirMeta extends Model
{
    protected $fillable = ['field_name', 'field_display_name', 'field_type', 'is_unique', 'sortir_id'];
    public function sortir(){
        return $this->belongsTo('App\Sortir', 'sortir_id', 'id');
    }
}
