<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'account_id', 'department_id', 'budget','notes',
        'company_id', 'budget_year', 'budget_month','created_by', 'updated_by', 'deleted_by'
    ];
    public function account(){
        return $this->belongsTo('App\Account');
    }
    public function department(){
        return $this->belongsTo('App\Department');
    }
}
