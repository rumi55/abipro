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
            'name'=>'Jurnal Umum',
            'company_id'=>$company_id,
            'format'=>'J[c].[m].[Y]',
            'counter_digit'=>6,
            'counter_reset'=>'y',
            'counter_start'=>1,
            'transaction_type_id'=>TransactionType::JOURNAL, 
        ]);
        Numbering::create([
            'name'=>'Jurnal Umum',
            'company_id'=>$company_id,
            'format'=>'ADJ-[c]/[m]/[Y]',
            'counter_digit'=>6,
            'counter_reset'=>'y',
            'counter_start'=>1,
            'transaction_type_id'=>TransactionType::JOURNAL, 
        ]);
        // Numbering::create([
        //     'name'=>'Penerimaan',
        //     'company_id'=>$company_id,
        //     'format'=>'CI-[c]/[m]/[Y]',
        //     'counter_digit'=>6,
        //     'counter_reset'=>'y',
        //     'counter_start'=>1,
        //     'transaction_type_id'=>TransactionType::CASHIN, 
        // ]);
        // Numbering::create([
        //     'name'=>'Pembayaran',
        //     'company_id'=>$company_id,
        //     'format'=>'CO-[c]/[m]/[Y]',
        //     'counter_digit'=>6,
        //     'counter_reset'=>'y',
        //     'counter_start'=>1,
        //     'transaction_type_id'=>TransactionType::CASHOUT, 
        // ]);
        // Numbering::create([
        //     'name'=>'Biaya',
        //     'company_id'=>$company_id,
        //     'format'=>'EXP-[c]/[m]/[Y]',
        //     'counter_digit'=>6,
        //     'counter_reset'=>'y',
        //     'counter_start'=>1,
        //     'transaction_type_id'=>TransactionType::EXPENSE, 
        // ]);
        // Numbering::create([
        //     'name'=>'Produk',
        //     'company_id'=>$company_id,
        //     'format'=>'P-[c]',
        //     'counter_digit'=>6,
        //     'counter_reset'=>'n',//no reset
        //     'counter_start'=>1,
        //     'transaction_type_id'=>TransactionType::PRODUCT, 
        // ]);
    }
}
