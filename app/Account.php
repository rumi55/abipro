<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    // use SoftDeletes;
    protected $fillable = [
        'account_no','account_name', 'account_name_en', 'account_parent_id',
        'company_id', 'account_type_id','has_children',
        'created_by', 'updated_by', 'deleted_by', 'tree_level',
        'op_debit', 'op_credit', 'op_date', 'sequence'
    ];

    public function company(){
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }
    public function accountType(){
        return $this->belongsTo('App\AccountType', 'account_type_id', 'id');
    }
    public function parent(){
        return $this->belongsTo('App\Account', 'account_parent_id', 'id');
    }
    public function children(){
        return $this->hasMany('App\Account', 'account_parent_id', 'id');
    }

    public function openingBalance(){
        $company = $this->company;
        $period = $company->getPeriod();
        return Balance::where('company_id', $this->company_id)
        ->where('account_id', $this->id)->first();
    }

    public function budget($start_month, $end_month){
        $start_month = \Carbon\Carbon::parse($start_month);
        $end_month = \Carbon\Carbon::parse($end_month);
        $current_month = $start_month;
        $budgets = array();
        while($current_month<=$end_month){
            $month = $current_month->format('Y-m');
            $col = $current_month->format('Y_m');
            $budget = Budget::where('company_id', $this->company_id)
            ->where('account_id', $this->id)
            ->where('budget_month', $month)
            ->first();
            if($budget==null){
                $budgets[] = array(
                    'budget_month'=>$month,
                    'budget'=>0
                );
            }else{
                $budgets[] = array(
                    'budget_month'=>$budget->budget_month,
                    'budget'=>$budget->budget
                );
            }
            $current_month = $current_month->addMonth();
        }
        return $budgets;
    }

    public function transaction(){
        $details = JournalDetail::join('journals', 'journal_details.journal_id', '=', 'journals.id')
        ->where('account_id', $this->id)
        ->orderBy('journals.trans_date')->get();
        return $details;
    }
    public function isLocked(){
        if(!$this->has_children){
            return JournalDetail::where('account_id', $this->id)->exists() || \App\Transaction::where('account_id', $this->id)->exists() || \App\TransactionDetail::where('account_id', $this->id)->exists()  || \App\Contact::where('account_receivable', $this->id)->exists() || \App\Contact::where('account_payable', $this->id)->exists();
        }else{
            return $this->checkTransaction($this->id);
        }
    }
    public function checkTransaction($id){
        $accounts = Account::where('account_parent_id', $id)->get();
        foreach($accounts as $account){
            if($account->has_children){
                return $this->checkTransaction($account->id);
            }else{
                return JournalDetail::where('account_id', $account->id)->exists();
            }
        }
    }
    public function sequence(){
        $seq = Account::where('company_id', $company_id)->whereNull('account_parent_id')->orderBy('sequence', 'desc')->first();
        if($seq==null){
            $sequence = 1000;
        }else{
            $sequence = $seq->sequence+1;
        }
    }
}
