<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    protected $fillable = [
        'is_quote', 'is_order', 'is_invoice',
        'sequence','product_id','quantity','unit_price','disc',
        'tax_id','amount','total_price','created_by', 'sales_id'
    ];
    protected $table = 'sales_details';
    
    public function product(){
        return $this->belongsTo('App\Product');
    }
    public function tax(){
        return $this->belongsTo('App\Tax');
    }
}
