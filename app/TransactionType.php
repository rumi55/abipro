<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    
    protected $fillable = ['id', 'display_name'];
    const KEY = 'transaction_type';
    const JOURNAL = 'journal';
    const VOUCHER = 'voucher';
    const EXPENSE = 'expense';
    const CASHIN = 'cashin';
    const CASHOUT = 'cashout';
    const PRODUCT = 'product';
    const CONTACT = 'contact';
}
