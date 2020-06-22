<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyType extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = ['id', 'display_name'];
    const SERVICE = 'ser';
    const MANUFACTURE = 'man';
    const TRADE = 'tra';
}
