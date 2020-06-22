<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClosingBook extends Model
{
    protected $table = 'closing_books';
    protected $fillable = ['start_date', 'end_date', 'account_id','profit', 'debit', 'credit', 'notes', 'company_id','status', 'created_by', 'updated_by'];
}
