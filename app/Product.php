<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'custom_id', 'name', 'product_category_id', 
        'unit_id', 'image', 'company_id', 'created_by', 'description',
        'buy_price', 'buy_account_id', 'sale_price', 'sell_account_id', 'numbering_id'
    ];

    public function company(){
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }
    public function unit(){
        return $this->belongsTo('App\ProductUnit', 'unit_id', 'id');
    }
    public function category(){
        return $this->belongsTo('App\ProductCategory', 'product_category_id', 'id');
    }
    public function isLocked(){
        return false;
    }
}
