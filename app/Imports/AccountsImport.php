<?php

namespace App\Imports;

use App\Account;
use App\Balance;
use Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AccountsImport implements ToCollection, WithHeadingRow
{
    private $company_id;
    private $company;
    private $user_id;
    public function __construct(int $company_id, int $user_id) 
    {
        $this->company_id = $company_id;
        $this->company = \App\Company::find($company_id);
        $this->user_id = $user_id;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        // try{
        //     \DB::beginTransaction();        
            foreach($rows as $row){
                // dd($row );
                if((empty($row['account_no']) && empty($row['account_name']) && empty($row['account_type']))){
                    continue;
                }
                $account_no = $row['account_no'];
                $account_name = $row['account_name'];
                $account_type = $row['account_type'];
                $parent_no = $row['account_parent_no'];
                $balance = $row['opening_balance'];
                $balance_date = $this->company->accounting_start_date;
                $sequence = '';
                if(empty($parent_no)){
                    $parent_id = null;
                    $tree_level = 0;
                    $seq = Account::where('company_id', $this->company_id)
                    ->whereNull('account_parent_id')
                    ->where('account_type_id', $account_type)
                    ->orderBy('sequence', 'desc')->first();
                    if($seq==null){
                        $sequence = $account_type*1000;
                    }else{
                        $sequence = $seq->sequence+1;
                    }
                }else{
                    //cari parent
                    $parent = Account::where('company_id', $this->company_id)->where('account_no', $parent_no)->first();
                    if($parent!=null){
                        $parent_id = $parent->id;
                        $parent->has_children = true;
                        $parent->save();
                        $tree_level = $parent->tree_level+1;
                        $seq = Account::where('company_id', $this->company_id)
                        ->where('account_parent_id', $parent->id)
                        ->orderBy('sequence', 'desc')->first();
                        if($seq!=null){
                            $ex = explode('.',$seq->sequence);
                            foreach($ex as $i => $x){
                                if($i==count($ex)-1){
                                    $sequence .= (intval($x)+1);
                                }else{
                                    $sequence .= $x.'.';
                                }
                            }
                        }else{
                            $sequence = $parent->sequence.'.1000';
                        }
                    }
                }
                // dd($parent_id.'-'.$row['nama']);
                $account = Account::updateOrCreate([
                    'account_no'=>$account_no.'',
                    'company_id'=>$this->company_id
                ],
                [
                    'sequence'=>$sequence,
                    'account_no'=>$account_no.'',
                    'account_name'=>trim($account_name),
                    'account_type_id'=>$account_type,
                    'account_parent_id'=>$parent_id,
                    'tree_level'=>$tree_level,
                    'has_children'=>false,
                    'company_id'=>$this->company_id
                ]);
                
                if(!empty($balance) && !empty($balance_date)){
                    $balance=intval($balance);
                    if($balance<0 && $account->accountType->debit_sign<0){
                        $debit = abs($balance);
                        $credit = 0;
                    }else if($balance<0 && $account->accountType->debit_sign>0){
                        $credit = abs($balance);
                        $debit = 0;
                    }else if($balance>0 && $account->accountType->debit_sign<0){
                        $credit = abs($balance);
                        $debit = 0;
                    }else if($balance>0 && $account->accountType->debit_sign>0){
                        $debit = abs($balance);
                        $credit = 0;
                    }
                    
                    if($balance!=0){
                        $account->op_debit = $debit;
                        $account->op_credit = $credit;
                        $account->op_date = fdate($balance_date, 'Y-m-d');
                        $account->save();
                        Balance::updateOrCreate([
                            'account_id'=>$account->id,
                            'company_id'=>$this->company_id,
                            'balance_date'=>fdate($balance_date, 'Y-m-d'),
                            'balance_year'=>fdate($balance_date, 'Y'),
                            'balance_period'=>fdate($balance_date, 'm'),
                        ],
                        [
                            'account_id'=>$account->id,
                            'company_id'=>$this->company_id,
                            'balance_date'=>fdate($balance_date, 'Y-m-d'),
                            'balance_year'=>fdate($balance_date, 'Y'),
                            'balance_period'=>fdate($balance_date, 'm'),
                            'balance'=>$balance,
                            'debit'=>$debit,
                            'credit'=>$credit,
                        ]);
                    }
                }    
            }
        // }catch(Exception $e){
        //     \DB::rollback();
        // }
    }
}