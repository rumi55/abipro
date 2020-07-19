<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportData extends Model
{
    protected $fillable=['company_id', 'created_by', 'target', 'file', 'status', 'created_at'];
}
