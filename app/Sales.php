<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $fillable = [
        'quote_date', 'order_date', 'invoice_date', 'quote_due_date', 'order_due_date', 'invoice_due_date', 
        'quote_no', 'order_no', 'invoice_no', 'is_quote', 'is_order', 'is_invoice',
        'status_quote', 'status_order', 'status_invoice',
        'quote_notes', 'quote_subtotal', 'quote_tax', 'quote_disc', 'quote_total', 'quote_total_disc','quote_term_id', 
        'order_notes', 'order_subtotal', 'order_tax', 'order_disc', 'order_total', 'order_total_disc','order_term_id', 
        'invoice_notes', 'invoice_subtotal', 'invoice_tax', 'invoice_disc', 'invoice_total', 'invoice_total_disc','invoice_term_id', 
        'currency_id', 'currency_rate',
        'salesman_id', 'customer_id', 'sales_id', 'company_id', 'department_id', 'warehouse_id'
    ];

    protected $table = 'sales';

    public function details(){
        return $this->hasMany('App\SalesDetail');
    }
    public function customer(){
        return $this->belongsTo('App\Contact', 'customer_id', 'id');
    }
    public function salesman(){
        return $this->belongsTo('App\Contact', 'salesman_id', 'id');
    }
    public function quoteTerm(){
        return $this->belongsTo('App\Term', 'quote_term_id', 'id');
    }
    public function orderTerm(){
        return $this->belongsTo('App\Term', 'order_term_id', 'id');
    }
    public function invoiceTerm(){
        return $this->belongsTo('App\Term', 'invoice_term_id', 'id');
    }
}
