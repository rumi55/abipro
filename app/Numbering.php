<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Numbering extends Model
{
    
    protected $fillable = ['name', 'format', 'counter_reset', 'counter_digit','company_id', 'transaction_type_id', 'counter_start'];

    public function transactionType(){
        return $this->belongsTo('App\TransactionType', 'transaction_type_id', 'id');
    }

    public function counter(){
        return $this->hasMany('App\Counter');
    }

    public static function createDefault($company_id){
        Numbering::create([
            'name'=>'Adjustment Journal',
            'company_id'=>$company_id,
            'format'=>'ADJ-[c]/[m]/[Y]',
            'counter_digit'=>6,
            'counter_reset'=>'y',
            'counter_start'=>1,
            'transaction_type_id'=>TransactionType::JOURNAL, 
        ]);
        Numbering::create([
            'name'=>'Bank In Journal',
            'company_id'=>$company_id,
            'format'=>'BI-[c]/[m]/[Y]',
            'counter_digit'=>6,
            'counter_reset'=>'y',
            'counter_start'=>1,
            'transaction_type_id'=>TransactionType::JOURNAL, 
        ]);
        Numbering::create([
            'name'=>'Bank Out Journal',
            'company_id'=>$company_id,
            'format'=>'BO-[c]/[m]/[Y]',
            'counter_digit'=>6,
            'counter_reset'=>'y',
            'counter_start'=>1,
            'transaction_type_id'=>TransactionType::JOURNAL, 
        ]);
        Numbering::create([
            'name'=>'Cash In Journal',  
            'company_id'=>$company_id,
            'format'=>'CI-[c]/[m]/[Y]',
            'counter_digit'=>6,
            'counter_reset'=>'y',
            'counter_start'=>1,
            'transaction_type_id'=>TransactionType::JOURNAL, 
        ]);
        Numbering::create([
            'name'=>'Cash Out Journal',
            'company_id'=>$company_id,
            'format'=>'CO-[c]/[m]/[Y]',
            'counter_digit'=>6,
            'counter_reset'=>'y',
            'counter_start'=>1,
            'transaction_type_id'=>TransactionType::JOURNAL, 
        ]);
        Numbering::create([
            'name'=>'Expenses Journal',
            'company_id'=>$company_id,
            'format'=>'EXP-[c]/[m]/[Y]',
            'counter_digit'=>6,
            'counter_reset'=>'y',
            'counter_start'=>1,
            'transaction_type_id'=>TransactionType::JOURNAL, 
        ]);
        Numbering::create([
            'name'=>'Operational Expenses Journal',
            'company_id'=>$company_id,
            'format'=>'OPS-[c]/[m]/[Y]',
            'counter_digit'=>6,
            'counter_reset'=>'y',
            'counter_start'=>1,
            'transaction_type_id'=>TransactionType::JOURNAL, 
        ]);
        Numbering::create([
            'name'=>'Customer ID',
            'company_id'=>$company_id,
            'format'=>'CUST-[c]',
            'counter_digit'=>6,
            'counter_reset'=>'n',
            'counter_start'=>1,
            'transaction_type_id'=>TransactionType::CONTACT, 
        ]);
        Numbering::create([
            'name'=>'Supplier ID',
            'company_id'=>$company_id,
            'format'=>'SUP-[c]',
            'counter_digit'=>6,
            'counter_reset'=>'n',
            'counter_start'=>1,
            'transaction_type_id'=>TransactionType::CONTACT, 
        ]);
        Numbering::create([
            'name'=>'Employee ID',
            'company_id'=>$company_id,
            'format'=>'EMP-[c]',
            'counter_digit'=>6,
            'counter_reset'=>'n',
            'counter_start'=>1,
            'transaction_type_id'=>TransactionType::CONTACT, 
        ]);
        Numbering::create([
            'name'=>'Product ID',
            'company_id'=>$company_id,
            'format'=>'PROD-[c]',
            'counter_digit'=>6,
            'counter_reset'=>'n',
            'counter_start'=>1,
            'transaction_type_id'=>TransactionType::PRODUCT, 
        ]);
    }
}
