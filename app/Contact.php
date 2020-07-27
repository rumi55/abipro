<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable =['title', 'name', 'custom_id', 'email', 'mobile', 'fax',
    'phone','tax_no','address', 'company', 'is_customer', 'is_archive',
    'is_supplier','is_employee', 'is_others','company_id', 'created_by', 'numbering_id',
    'account_receivable', 'account_payable', 'opening_balance_ar', 'opening_balance_ap'
    ];
}
