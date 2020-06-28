<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = [
        'sales_quote_id','account_id', 'numbering_id', 'trans_no', 'trans_date',
        'due_date', 'term_id', 'customer_id', 'salesman_id', 
        'description', 'subtotal', 'tax', 'total', 'discount',
        'created_at', 'updated_at', 'created_by', 'updated_by', 'company_id'
    ];
    public function details(){
        return $this->hasMany('App\SalesOrderDetail');
    }
    public function salesQuote(){
        return $this->belongsTo('App\SalesQuote');
    }
    public function customer(){
        return $this->belongsTo('App\Contact', 'customer_id', 'id');
    }
    public function salesman(){
        return $this->belongsTo('App\Contact', 'salesman_id', 'id');
    }
    public function createdBy(){
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
    public function updatedBy(){
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }
}
