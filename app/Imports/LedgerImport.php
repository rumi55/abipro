<?php

namespace App\Imports;

use App\Journal;
use App\JournalDetail;
use App\TransactionType;
use App\Numbering;
use App\Counter;
use App\Account;
use Auth;
use DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LedgerImport implements ToCollection, WithHeadingRow
{
    protected $company_id;
    protected $user_id;
    public function __construct(int $company_id, int $user_id) 
    {
        $this->company_id = $company_id;
        $this->user_id = $user_id;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        
        $company_id = $this->company_id;
        $user_id = $this->user_id;
        
        try{
            \DB::beginTransaction();
        foreach($rows as $i => $row){
            $trans_date = fdate($row['transaction_date'], 'Y-m-d');
            $trans_no = $row['transaction_no'];
            $description = $row['description'];
            $account_no = $row['account_no'];
            $created_at = fdate($row['transaction_date'], 'Y-m-d H:i:s');
            $sequence = $row['sequence'];
            $debit = abs($row['debit']);
            $credit = abs($row['credit']);
            $total = abs($row['total']);
            //account
            $account = Account::where('company_id', $company_id)->where('account_no', $account_no)->first();
            if($account==null){
                continue;
            }
            $journal = Journal::where('company_id', $company_id)->where('trans_no', $trans_no)->first();

            if($journal==null){
                $numbering = Numbering::findOrFail(1);
                if($numbering->counter_reset=='y'){
                    $period = date('Y');
                }else if($numbering->counter_reset=='m'){
                    $period = date('Y-m');
                }else if($numbering->counter_reset=='d'){
                    $period = date('Y-m-d');
                }else{
                    $period  = null;
                }
                $counter = Counter::firstOrCreate(
                    ['period'=>$period, 'numbering_id'=>$numbering->id, 'company_id'=>$company_id],
                    ['counter'=>$numbering->counter_start-1]
                );        
                
                $check = true;
                do{
                    $counter->getNumber();
                    $jtrans_no = $counter->last_number;
                    $c = Journal::where('trans_no', $trans_no)->where('company_id', $company_id)->count(); 
                    
                    if($c==0){
                        $journal = Journal::create([
                            'journal_id'=>$jtrans_no,
                            'trans_no'=>$trans_no,
                            'trans_date'=>$trans_date,
                            'description'=>$description,
                            'company_id'=>$company_id,
                            'is_voucher'=>0,
                            'total'=>$total,
                            'transaction_type_id'=>TransactionType::JOURNAL,
                            'created_by'=>$user_id,
                            'created_at'=>$created_at
                        ]);
                        $counter->save();
                        $check = false;
                    }
                }while($check);                
            }
            
            JournalDetail::create([
                'sequence'=>$sequence,
                'account_id'=>$account->id,
                'description'=>$description,
                'department_id'=>null,
                'debit'=>$debit,
                'credit'=>$credit,
                'journal_id'=>$journal->id,
                'created_by'=>$user_id,
                'created_at'=>$created_at
            ]);

        }
        \DB::commit();        
        }catch(Exception $e){
            \DB::rollback();
        }

    }
}
