<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JournalDetail extends Model
{
    protected $fillable = [
        'sequence', 'description', 'account_id', 'journal_id', 'trans_date',
        'debit', 'credit', 'created_by', 'updated_by', 'department_id', 'tags'
    ];
    public function account(){
        return $this->belongsTo('App\Account', 'account_id', 'id');
    }
    public function journal(){
        return $this->belongsTo('App\Journal', 'journal_id', 'id');
    }
    public function department(){
        return $this->belongsTo('App\Department', 'department_id', 'id');
    }
    public function getTags(){
        $tag_id= explode(',', $this->tags);
        return Tag::whereIn('id', $tag_id)->get();
    }
}
