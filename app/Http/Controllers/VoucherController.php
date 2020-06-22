<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\JournalResource;
use App\Exceptions\ApiValidationException;
use App\Counter;
use App\Journal;
use App\JournalDetail;
use App\Numbering;
use App\TransactionType;
use Auth;
use Validator;
use PDF;

class VoucherController extends Controller
{
    public function getAll(Request $request){
        return $this->query($request);
    }
    public function getAllJournal(Request $request){
        return $this->query($request, 1);
    }
    
    public function query(Request $request, $is_journal=0){
        $company_id = Auth::user()->activeCompany()->id;
        $journal = Journal::where('company_id', '=', $company_id)
        ->where('transaction_type_id', '=', 'voucher');
        $page_size = $request->query('page_size', $journal->count());
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter');
        if(isset($filter)){
            foreach($filter as $column => $value){
                $journal = $journal->where($column,'like', "%$value%");
            }
        }
        if(!empty($search)){
            $journal = $journal->where(function ($query) use($search){
                $query->where('trans_no','like', "%$search%")
                ->orWhere('trans_date','like', "%$search%")
                ->orWhere('description','like', "%$search%");
            });
        }

        $sort = $request->query('sort');
        if(!empty($sort)){
            $sort = explode('-',$sort);
            $sort_key=$sort[0];
            $sort_order=count($sort)==2?(substr($sort[1], 0, 3)=='asc'?'asc':'desc'):'asc';
            $journal = $journal->orderBy($sort_key, $sort_order);
        }    
        
        if(!empty($sort_key)){
            $journal = $journal->orderBy($sort_key, $sort_order);
        }
        if(empty($sort_key) && empty($sort)){
            $journal = $journal->orderBy('trans_date', 'desc');
        }
        $journal = $journal->paginate($page_size)->appends($request->query());
        return JournalResource::collection($journal);
    }
    public function get($id){
        $id = decode($id);
        return new JournalResource(Journal::findOrFail($id));
    }
    
    public function create(Request $request){
        // return $request->description;
        validate($request->all(), [
            'trans_no' => 'required',
            'trans_date' => 'required',
            'description' => 'required',
        ]);
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $trans_date = fdate($request->trans_date, 'Y-m-d');
        // return $request->details;
        try{
            \DB::beginTransaction();
            if($request->auto){
                $numbering = Numbering::findOrFail(decode($request->trans_no));
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
                    $trans_no = $counter->last_number;
                    $c = Journal::where('trans_no', $counter->last_number)->where('company_id', $company_id)->count(); 
                    if($c==0){
                        $journal = Journal::create([
                            'journal_id'=>$trans_no,
                            'trans_no'=>$trans_no,
                            'trans_date'=>$trans_date,
                            'description'=>$request->description,
                            'company_id'=>$company_id,
                            'total'=>$request->total,
                            'transaction_type_id'=>TransactionType::VOUCHER,
                            'created_by'=>$user->id
                        ]);
                        $counter->save();
                        $check = false;
                    }
                }while($check);                
            }else{
                $trans_no = $request->trans_no;
                $journal = Journal::create([
                    'journal_id'=>$trans_no,
                    'trans_no'=>$trans_no,
                    'trans_date'=>$trans_date,
                    'description'=>$request->description,
                    'company_id'=>$company_id,
                    'total'=>$request->total,
                    'transaction_type_id'=>TransactionType::VOUCHER,
                    'created_by'=>$user->id
                ]);
            }
            foreach($request->details as $detail){
                JournalDetail::create([
                    'sequence'=>$detail['sequence'],
                    'account_id'=>decode($detail['account_id']),
                    'description'=>$detail['description'],
                    'department_id'=>decode($detail['department_id']),
                    'debit'=>$detail['debit'],
                    'credit'=>$detail['credit'],
                    'journal_id'=>$journal->id,
                    'created_by'=>$user->id
                ]);    
            }
            \DB::commit();        
        }catch(Exception $e){
            \DB::rollback();
        }

        return (new JournalResource($journal))
                ->response()
                ->setStatusCode(201);
    }
    public function update(Request $request, $id){
        $id = decode($id);
        $journal = Journal::findOrFail($id);

        validate($request->all(), [
            'trans_no' => 'required',
            'trans_date' => 'required',
            'description' => 'required',
        ]);

        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $trans_date = fdate($request->trans_date, 'Y-m-d');
        
        $journal->trans_date = $trans_date;
        $journal->description = $request->description;
        $journal->total = $request->total;
        $journal->updated_by = $user->id;
        
        try{
            \DB::beginTransaction();
            if($request->auto){
                $numbering = Numbering::findOrFail(decode($request->trans_no));
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
                    $trans_no = $counter->last_number;
                    $c = Journal::where('trans_no', $counter->last_number)->where('company_id', $company_id)->count(); 
                    if($c==0){                
                        $journal->journal_id = $trans_no;
                        $journal->trans_no = $trans_no;
                        $journal->update();
                        $counter->save();
                        $check = false;
                    }
                }while($check);                
            }else{
                $trans_no = $request->trans_no;
                $journal->update();
            }
            //cek id 
            $old_details = array();

            foreach($journal->details as $detail){
                $old_details[$detail->id] = $detail;
            }
            foreach($request->details as $detail){
                if($detail['id']==null){
                    JournalDetail::updateOrcreate([
                        'sequence'=>$detail['sequence'],
                        'account_id'=>decode($detail['account_id']),
                        'description'=>$detail['description'],
                        'department_id'=>$detail['department_id'],
                        'debit'=>$detail['debit'],
                        'credit'=>$detail['credit'],
                        'journal_id'=>$journal->id,
                        'created_by'=>$user->id
                    ]);    
                }else{
                    $jid = decode($detail['id']);
                    $jdetail = JournalDetail::findOrFail($jid);
                    $jdetail->sequence=$detail['sequence'];
                    $jdetail->account_id=decode($detail['account_id']);
                    $jdetail->description=$detail['description'];
                    $jdetail->department_id=decode($detail['department_id']);
                    $jdetail->debit=$detail['debit'];
                    $jdetail->credit=$detail['credit'];
                    $jdetail->journal_id=$journal->id;
                    $jdetail->updated_by=$user->id;
                    $jdetail->update();
                    if(array_key_exists($jid, $old_details)){
                       unset($old_details[$jid]); 
                    }
                }
            }
            foreach($old_details as $detail){
                $detail->delete();
            }
            \DB::commit();        
        }catch(Exception $e){
            \DB::rollback();
        }        
        return new JournalResource($journal);
    }
    public function patch(Request $request, $id){
        $decoid = decode($id);
        $journal = Journal::findOrFail($decoid);

        validate($request->all(), [
            'name' => 'required',
            'value' => 'required',
        ]);
        $name = $request->name;
        $value = $request->value;

        $journal->$name = $value;
        $journal->update();
        return new JournalResource($journal);
    }
    public function delete($id)
    {
        $id = decode($id);
        $journal = Journal::findOrFail($id);
        $journal->delete();
        return response()->json(null, 204);
    }
    

    public function batchDelete(Request $request){
        $id = $request->id;
        $ids = array();
        foreach($id as $i){
            $ids[] = decode($i);
        }
        Journal::destroy($ids);
        return response()->json(null, 204);
    }
    public function toJournal($id){
        $id = decode($id);
        $journal = Journal::findOrFail($id);
        $journal->is_journal=true;
        $journal->journal_transfer_at = date('Y-m-d H:i:s');
        $journal->save();
        return new JournalResource($journal);
    }
    public function toVoucher($id){
        $id = decode($id);
        $journal = Journal::findOrFail($id);
        $journal->is_journal=false;
        $journal->journal_transfer_at = null;
        $journal->save();
        return new JournalResource($journal);
    }

    public function toJournalBatch(Request $request){
        $id = $request->id;
        $ids = array();
        foreach($id as $i){
            $ids[] = decode($i);
        }
        Journal::whereIn('id', $ids)->where('balance','=', 0)->update(['is_journal'=>true, 'journal_transfer_at'=>date('Y-m-d H:i:s')]);
        return JournalResource::collection(Journal::whereIn('id', $ids)->get());
    }
    public function toVoucherBatch(Request $request){
        $id = $request->id;
        $ids = array();
        foreach($id as $i){
            $ids[] = decode($i);
        }
        Journal::whereIn('id', $ids)->where('balance','=', 0)->update(['is_journal'=>false, 'journal_transfer_at'=>null]);
        return JournalResource::collection(Journal::whereIn('id', $ids)->get());
    }
    
}