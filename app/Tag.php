<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable =['tag', 'group', 'item_id', 'item_name' ,'company_id', 'created_by', 'updated_by'];
}
