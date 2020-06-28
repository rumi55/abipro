<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesInvoiceDetail extends Model
{
    protected $fillable = [
        'sales_invoice_id','sequence', 'product_id', 'description', 'quantity',
        'unit_id', 'unit_price', 'discount', 'tax_id', 'amount','discount_percent','tax',
        'created_at', 'updated_at', 'created_by', 'updated_by'
    ];
    public function salesInvoice(){
        return $this->belongsTo('App\SalesInvoice');
    }
    public function product(){
        return $this->belongsTo('App\Product');
    }
    public function unit(){
        return $this->belongsTo('App\ProductUnit', 'unit_id', 'id');
    }
    public function taxes(){
        return $this->belongsTo('App\Tax');
    }
}
