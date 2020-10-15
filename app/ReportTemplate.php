<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportTemplate extends Model
{
    protected $table = 'report_templates';
    protected $fillable = [
        'report_name', 'template_name', 'template_content', 'company_id', 'created_by', 'created_at', 'is_default'
    ];
}
