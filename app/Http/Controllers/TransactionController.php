<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\JournalResource;
use App\Exceptions\ApiValidationException;
use App\Counter;
use App\Journal;
use App\Transaction;
use App\TransactionDetail;
use App\JournalDetail;
use App\Numbering;
use App\TransactionType;
use Auth;
use Validator;
use PDF;

class TransactionController extends Controller
{
    public function index(){
        $data = dcru_dt('vouchers', 'dtables');
        return view('transaction.index', $data);
    }

    public function view($id){
        $company_id = company('id');
        $transaction = Transaction::findOrFail($id);
        if($transaction->company_id!=$company_id){
            abort(404);
        }
        $prev=Transaction::where('company_id', $transaction->company_id)
        ->where('id','<', $transaction->id)->orderBy('id', 'desc')->first();
        $next=Transaction::where('company_id', $transaction->company_id)
        ->where('id','>', $transaction->id)->orderBy('id', 'asc')->first();
        $prev_id = $prev!=null?$prev->id:'';
        $next_id = $next!=null?$next->id:'';
        return view('transaction.view', compact('transaction', 'next_id', 'prev_id'));
    }

    public function reportTransaction(Request $request){
        $data = $this->query($request, 1);
        // return view('pdf.Transaction.report', array('Transactions'=>$data));
        $pdf = PDF::loadView('pdf.Transaction.report', array('Transactions'=>$data, 'title'=>'Laporan Jurnal'));
        return $pdf->download('Transaction.pdf');
        // return $pdf->stream('Transaction.pdf');
    }

    public function report(Request $request){
        $data = $this->query($request);
        // $period_start = $request->query('period_end');
        // $period_end = $request->query('period_start');
        // return view('pdf.Transaction.report', array('Transactions'=>$data, 'title'=>'Laporan Transaction'));
        $pdf = PDF::loadView('pdf.Transaction.report', array('Transactions'=>$data, 'title'=>'Laporan Transaction'));
        return $pdf->download('Transactions.pdf');
        // return $pdf->stream('Transaction.pdf');
    }

    public function create(Request $request, $type){
        $company_id = \Auth::user()->activeCompany()->id;
        $transaction = new Transaction;
        $transaction->trans_type = $type;
        $accounts = \App\Account::where('company_id', $company_id)
        ->where('has_children', false)->orderBy('account_type_id')->get();
        $departments = \App\Department::where('company_id', $company_id)->get();
        $contacts = \App\Contact::where('company_id', $company_id)->orderBy('name')->get();

        $tags = \App\Tag::where('company_id', $company_id)->orderBy('group')->orderBy('item_id')->get();
        $numberings = \App\Numbering::where('company_id', $company_id)
        ->where('transaction_type_id', TransactionType::JOURNAL)->get();
        $mode = 'create';
        return view('transaction.form', compact('transaction', 'mode', 'accounts', 'contacts','departments', 'numberings', 'tags'));
    }
    public function duplicate(Request $request, $id){
        $company_id = \Auth::user()->activeCompany()->id;
        $transaction = Transaction::findOrFail($id);
        if($transaction->company_id != $company_id){
            abort(404);
        }
        $accounts = \App\Account::where('company_id', $company_id)
        ->where('has_children', false)->orderBy('account_type_id')->get();
        $departments = \App\Department::where('company_id', $company_id)->get();
        $contacts = \App\Contact::where('company_id', $company_id)->orderBy('name')->get();
        $tags = \App\Tag::where('company_id', $company_id)->orderBy('group')->orderBy('item_id')->get();
        $numberings = \App\Numbering::where('company_id', $company_id)
        ->where('transaction_type_id', TransactionType::JOURNAL)->get();
        $mode = 'create';
        return view('transaction.form', compact('transaction', 'mode', 'accounts', 'contacts','departments', 'numberings', 'tags'));
    }
    public function edit(Request $request, $id){
        $company_id = company('id');
        $transaction = Transaction::findOrFail($id);
        if($transaction->company_id != $company_id){
            abort(404);
        }
        $accounts = \App\Account::where('company_id', $company_id)
        ->where('has_children', false)->orderBy('account_type_id')->get();
        $departments = \App\Department::where('company_id', $company_id)->get();
        $contacts = \App\Contact::where('company_id', $company_id)->orderBy('name')->get();
        $tags = \App\Tag::where('company_id', $company_id)->orderBy('group')->orderBy('item_id')->get();
        $numberings = \App\Numbering::where('company_id', $company_id)
        ->where('transaction_type_id', TransactionType::JOURNAL)->get();
        $mode = 'edit';
        return view('transaction.form', compact('transaction', 'mode', 'accounts', 'contacts','departments', 'numberings', 'tags'));
    }
    public function save(Request $request, $type){
        $data = $request->all();
        // dd($data);
        $detail_rules = [
            'detail_account_id'=>'required',
            'detail_description'=>'required',
            'detail_tags'=>'nullable|max:255',
            // 'detail_department_id'=>'required',
            'detail_amount'=>'required',
        ];
        $detail_attr = [
            'detail_account_id'=>'Akun',
            'detail_description'=>'Keterangan',
            // 'detail_department_id'=>'Departemen',
            'detail_tags'=>'Sortir',
            'detail_amount'=>'Jumlah'
        ];

        $rules = [
            'numbering_id' => 'required|exists:numberings,id',
            'trans_date' => 'required|date_format:d-m-Y',
            'description' => 'nullable|min:3|max:255',
            'account_id' => 'required|exists:accounts,id',
            'contact_id' => 'required|exists:contacts,id',
            'department_id' => 'nullable|exists:departments,id',
        ];
        $attr = [
            'numbering_id' => 'Grup Transaksi',
            'trans_no' => 'Nomor',
            'trans_date' => 'Tanggal',
            'description' => 'Keterangan',
            'account_id'=>'Akun',
            'department_id'=>'Departemen',
            'contact_id'=>'Kontak'
        ];
        $keys = array_keys($data);
        foreach($keys as $key){
            $f = substr($key, 0, strripos($key, '_'));
            if(in_array($f, ['detail_amount'])){
                $data[$key] = parse_number($data[$key]);
            }elseif(in_array($key, ['amount'])){
                $data[$key] = parse_number($data[$key]);
            }
            if(array_key_exists($f, $detail_rules)){
                $rules[$key] = $detail_rules[$f];
            }
            if(array_key_exists($f, $detail_attr)){
                $attr[$key] = $detail_attr[$f];
            }
        }

        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        // dd($data);
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $trans_date = fdate($request->trans_date, 'Y-m-d');

        try{
            \DB::beginTransaction();
            $auto = true;
            if($auto){
                $numbering = Numbering::findOrFail($request->numbering_id);
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
                    $c = Transaction::where('trans_no', $counter->last_number)->where('company_id', $company_id)->count();
                    if($c==0){
                        $transaction = Transaction::create([
                            'trans_no'=>$trans_no,
                            'trans_date'=>$trans_date,
                            'trans_type'=>$type,
                            'contact_id'=>$data['contact_id'],
                            'status'=>$data['status'],
                            'department_id'=>$data['department_id'],
                            // 'tags'=>$request->tags,
                            'description'=>$data['description'],
                            'company_id'=>$company_id,
                            'amount'=>$data['amount'],
                            'account_id'=>$data['account_id'],
                            'transaction_type_id'=>TransactionType::JOURNAL,
                            'created_by'=>$user->id
                        ]);
                        $counter->save();
                        $check = false;
                    }
                }while($check);
            }else{
                $trans_no = $request->trans_no;
                $transaction = Transaction::create([
                    'trans_no'=>$trans_no,
                    'trans_date'=>$trans_date,
                    'trans_type'=>$type,
                    'contact_id'=>$data['contact_id'],
                    'department_id'=>$data['department_id'],
                    // 'tags'=>$request->tags,
                    'description'=>$data['description'],
                    'company_id'=>$company_id,
                    'amount'=>$data['amount'],
                    'account_id'=>$data['account_id'],
                    'transaction_type_id'=>TransactionType::JOURNAL,
                    'created_by'=>$user->id
                ]);
            }
            $len = $request->detail_length;
            for($i=0;$i<$len;$i++){
                TransactionDetail::create([
                    'sequence'=>$i,
                    'account_id'=>$data["detail_account_id_$i"],
                    'amount'=>$data["detail_amount_$i"],
                    'description'=>$data["detail_description_$i"],
                    'department_id'=>$data["detail_department_id_$i"],
                    'tags'=>$data["detail_tags_$i"],
                    'transaction_id'=>$transaction->id,
                    'created_by'=>$user->id
                ]);
            }
            //
            // $this->addToJournal($transaction);
            if($transaction->status=='submitted'){
                notify([
                    'url'=>route('vouchers.view', $transaction->id),
                    'message_en'=>$transaction->createdBy->name.' submitted a new voucher',
                    'message'=>$transaction->createdBy->name.' mengajukan voucher baru',
                    'users'=>\App\User::getUsersHaveAction('vouchers', 'approve')
                ]);
            }
            $reference =[
                'id'=>$transaction->id,
                'Transaction No.'=>$transaction->trans_no,
                'Total'=>$transaction->total
            ];
            add_log('vouchers', 'create', json_encode($reference));
            \DB::commit();
        }catch(Exception $e){
            \DB::rollback();
        }

        return redirect()->route('dcru.index', 'vouchers')->with('success', 'Transaksi berhasil dibuat.');
    }
    public function update(Request $request, $type, $id){
        $transaction = Transaction::findOrFail($id);
        $data = $request->all();
        // dd($data);
        $detail_rules = [
            'detail_account_id'=>'required',
            'detail_description'=>'required',
            'detail_tags'=>'nullable|max:255',
            // 'detail_department_id'=>'required',
            'detail_amount'=>'required',
        ];
        $detail_attr = [
            'detail_account_id'=>'Akun',
            'detail_description'=>'Keterangan',
            // 'detail_department_id'=>'Departemen',
            'detail_tags'=>'Sortir',
            'detail_amount'=>'Jumlah'
        ];

        $rules = [
            'numbering_id' => 'required|exists:numberings,id',
            'trans_date' => 'required|date_format:d-m-Y',
            'description' => 'nullable|min:3|max:255',
            'account_id' => 'required|exists:accounts,id',
            'contact_id' => 'required|exists:contacts,id',
            'department_id' => 'nullable|exists:departments,id',
        ];
        $attr = [
            'numbering_id' => 'Grup Transaksi',
            'trans_no' => 'Nomor',
            'trans_date' => 'Tanggal',
            'description' => 'Keterangan',
            'account_id'=>'Akun',
            'department_id'=>'Departemen',
            'contact_id'=>'Kontak'
        ];
        $keys = array_keys($data);
        foreach($keys as $key){
            $f = substr($key, 0, strripos($key, '_'));
            if(in_array($f, ['detail_amount'])){
                $data[$key] = parse_number($data[$key]);
            }elseif(in_array($key, ['amount'])){
                $data[$key] = parse_number($data[$key]);
            }
            if(array_key_exists($f, $detail_rules)){
                $rules[$key] = $detail_rules[$f];
            }
            if(array_key_exists($f, $detail_attr)){
                $attr[$key] = $detail_attr[$f];
            }
        }

        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;

        $trans_date = fdate($request->trans_date, 'Y-m-d');

        $transaction->trans_date = $trans_date;
        $transaction->account_id = $data['account_id'];
        $transaction->contact_id = $data['contact_id'];
        $transaction->department_id = $data['department_id'];
        $transaction->description = $data['description'];
        // $transaction->tags = $request->tags;
        $transaction->amount = $data['amount'];
        $transaction->updated_by = $user->id;

        try{
            \DB::beginTransaction();
            $auto = false;//
            if($auto){
                $numbering = Numbering::findOrFail(decode($data['numbering_Id']));
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
                    $c = Transaction::where('trans_no', $counter->last_number)->where('company_id', $company_id)->count();
                    if($c==0){
                        $transaction->trans_no = $trans_no;
                        $transaction->update();
                        $counter->save();
                        $check = false;
                    }
                }while($check);
            }else{
                // $trans_no = $request->trans_no;
                $transaction->update();
            }
            //cek id
            $old_details = array();
            foreach($transaction->details as $detail){
                $old_details[$detail->id] = $detail;
            }
            $transaction_details=array();
            $len = $request->detail_length;
            for($i=0;$i<$len;$i++){
                if($data["detail_id_$i"]==null){
                    $jdetail= TransactionDetail::create([
                        'sequence'=>$i,
                        'account_id'=>$data["detail_account_id_$i"],
                        'amount'=>$data["detail_amount_$i"],
                        'description'=>$data["detail_description_$i"],
                        'department_id'=>$data["detail_department_id_$i"],
                        'tags'=>$data["detail_tags_$i"],
                        'transaction_id'=>$transaction->id,
                        'created_by'=>$user->id
                    ]);
                }else{
                    $jid = $data["detail_id_$i"];
                    $jdetail = TransactionDetail::findOrFail($jid);
                    $old_sq = $jdetail->sequence;
                    $jdetail->sequence=$i;
                    $jdetail->account_id=$data["detail_account_id_$i"];
                    $jdetail->amount=$data["detail_amount_$i"];
                    $jdetail->description=$data["detail_description_$i"];
                    $jdetail->department_id=$data["detail_department_id_$i"];
                    $jdetail->tags=$data["detail_tags_$i"];
                    $jdetail->transaction_id=$transaction->id;
                    $jdetail->updated_by=$user->id;
                    $jdetail->update();
                    if(array_key_exists($jid, $old_details)){
                       unset($old_details[$jid]);
                    }
                }
                $transaction_details[] = $jdetail;
            }
            foreach($old_details as $detail){
                $detail->delete();
            }
            // $this->updateJournal($transaction, $transaction_details);
            \DB::commit();
        }catch(Exception $e){
            \DB::rollback();
        }
        return redirect()->route('vouchers.view', $transaction->id)->with('success', trans('Changes have been saved.'));
    }
    public function approve(Request $request, $id){
        $company_id = company('id');
        $user = \Auth::user();
        $transaction = Transaction::findOrFail($id);
        if($transaction->company_id!=$company_id || !(in_array($request->status, ['approved', 'rejected', 'submitted'])) ){
            abort(401);
        }
        if($request->status=='submitted' && $user->id!=$transaction->created_by){
            abort(401);
        }
        $transaction->status = $request->status;
        $transaction->approved_at = date('Y-m-d H:i:s');
        $transaction->approved_by = user('id');
        $transaction->rejection_note = $request->rejection_note;
        $transaction->update();
        if($transaction->status=='approved'){
            $this->addToJournal($transaction);
            $msg = $user->name.' menyetujui voucher #'.$transaction->trans_no;
            $msg_en = $user->name.' approved voucher #'.$transaction->trans_no;
        }else
        if($transaction->status=='rejected'){
            $msg = $user->name.' menolak voucher #'.$transaction->trans_no;
            $msg_en = $user->name.' rejected voucher #'.$transaction->trans_no;
        }
        notify([
            'url'=>route('vouchers.view', $transaction->id),
            'message_en'=>$msg_en,
            'message'=>$msg,
            'users'=>[$transaction->created_by]
        ]);

        $msg = $transaction->status=='submitted'?trans('Voucher submitted!'):($transaction->status=='approved'?trans('Voucher approved!'):trans('Voucher rejected!'));
        return redirect()->route('vouchers.view', $transaction->id)->with('success', $msg);
    }
    public function patch(Request $request, $id){
        $decoid = decode($id);
        $transaction = Transaction::findOrFail($decoid);

        validate($request->all(), [
            'name' => 'required',
            'value' => 'required',
        ]);
        $name = $request->name;
        $value = $request->value;

        $transaction->$name = $value;
        $transaction->update();
        return new TransactionResource($transaction);
    }
    public function delete($id)
    {
        $company_id = company('id');
        $transaction = Transaction::findOrFail($id);
        if($transaction->company_id!=$company_id){
            abort(401);
        }
        $type = $transaction->transaction_type_id;
        $transaction->delete();
        return redirect()->route('vouchers.index')->with('success', trans(':attr has been deleted.', ['attr'=>trans('Voucher')]));
    }


    public function batchDelete(Request $request){
        $id = $request->id;
        $ids = array();
        foreach($id as $i){
            $ids[] = decode($i);
        }
        if(count($ids)>0){
            $type = Transaction::find($ids[0])->transaction_type_id;
            Journal::whereIn('transaction_id', $ids)
            ->where('transaction_type_id', $type)
            ->delete();
        }
        Transaction::destroy($ids);
        return redirect()->route('vouchers.index')->with('success', trans(':attr has been deleted.', ['attr'=>trans('Voucher')]));
    }


    private function updateJournal($transaction, $transaction_details){
        if($transaction->trans_type=='receipt'){
            $description = 'Penerimaan dari '.$transaction->contact->name;
        }else if($transaction->trans_type=='payment'){
            $description = 'Pengeluaran untuk '.$transaction->contact->name;
        }
        $journal = Journal::where('is_single_entry', 1)
                    ->where('transaction_id', $transaction->id)->first();
        if($journal==null){//tidak ada di jurnal
            return $this->addToJournal($transaction);
        }

        $journal->trans_no = $transaction->trans_no;
        $journal->trans_date = $transaction->trans_date;
        $journal->description = $description;
        // $journal->department_id = $transaction->department_id;
        // $journal->tags = $transaction->tags;
        $journal->total = $transaction->amount;
        $journal->updated_by = Auth::user()->id;
        $journal->save();

        $debit = 0;
        $credit = 0;
        if($transaction->trans_type=='receipt'){
            $debit = $transaction->amount;
            $credit = 0;
        }else if($transaction->trans_type=='payment'){
            $credit = $transaction->amount;
            $debit = 0;
        }

        $journal_detail = JournalDetail::where('journal_id', $journal->id)->where('sequence', 0)->first();
        $journal_detail->trans_date = $transaction->trans_date;
        $journal_detail->sequence = 0;
        $journal_detail->account_id = $transaction->account_id;
        $journal_detail->department_id = $transaction->department_id;
        $journal_detail->tags = $transaction->tags;
        $journal_detail->debit = $debit;
        $journal_detail->credit = $credit;
        $journal_detail->account_id=$transaction->account_id;
        $journal_detail->description=$description;
        $journal_detail->update();
        $old_details = $journal->details;
        $old_jdetails = array();
        foreach($old_details as $detail){
            $old_jdetails[$detail->id] = $detail;
        }
        unset($old_jdetails[$journal_detail->id]); //hilangkan index ke - 0 dari list

        $new_jdetails = array();
        foreach($transaction_details as $i =>$detail){
            $debit = 0;
            $credit = 0;
            if($transaction->trans_type=='receipt'){
                $credit = $detail->amount;
                $debit = 0;
            }else if($transaction->trans_type=='payment'){
                $debit = $detail->amount;
                $credit = 0;
            }
            $jdetail = JournalDetail::where('journal_id', $journal->id)->where('sequence', $detail->sequence+1)->first();
            if($jdetail==null){//ada tambahan baru
                $jdetail = new JournalDetail;
            }
            $jdetail->journal_id = $journal->id;
            $jdetail->trans_date = $journal->trans_date;
            $jdetail->sequence = $detail->sequence+1;
            $jdetail->department_id = $detail->department_id;
            $jdetail->tags = $detail->tags;
            $jdetail->debit = $debit;
            $jdetail->credit = $credit;
            $jdetail->account_id=$detail->account_id;
            $jdetail->description=$detail->description;
            $jdetail->save();
            $new_jdetails[$jdetail->id] = $jdetail;
        }
        foreach($old_jdetails as $detail){
            if(!array_key_exists($detail->id, $new_jdetails)){
                $detail->delete();
            }
        }

        return $journal;
    }

    public function toJournal(Request $request, $id){
        $transaction = Transaction::findOrFail(decode($id));
        $journal = Journal::where('transaction_type_id',$transaction->transaction_type_id)
                    ->where('transaction_id', $transaction->id)->first();
        if($journal!==null){
            return (new JournalResource($journal))
                ->response()
                ->setStatusCode(201);
        }
        try{
            \DB::beginTransaction();
            $journal = $this->addToJournal($transaction);
            \DB::commit();
        }catch(Exception $e){
            \DB::rollback();
        }

        return (new JournalResource($journal))
                ->response()
                ->setStatusCode(201);
    }

    private function addToJournal($transaction){
        if($transaction->trans_type=='receipt'){
            $description = 'Penerimaan dari '.$transaction->contact->name;
        }else if($transaction->trans_type=='payment'){
            $description = 'Pengeluaran untuk '.$transaction->contact->name;
        }

        //save to journal
        $jnumbering = Numbering::where('transaction_type_id', TransactionType::JOURNAL)->first();
        if($jnumbering->counter_reset=='y'){
            $period = date('Y');
        }else if($jnumbering->counter_reset=='m'){
            $period = date('Y-m');
        }else if($jnumbering->counter_reset=='d'){
            $period = date('Y-m-d');
        }else{
            $period  = null;
        }
        $jcounter = Counter::firstOrCreate(
                ['period'=>$period, 'numbering_id'=>$jnumbering->id, 'company_id'=>$transaction->company_id],
                ['counter'=>$jnumbering->counter_start-1]
        );
        $check = true;
        do{
            $jcounter->getNumber();
            $journal_id = $jcounter->last_number;
            $jc = Journal::where('journal_id', $journal_id)->where('company_id', $transaction->company_id)->count();
            if($jc==0){
                $journal = Journal::create([
                    'journal_id'=>$journal_id,
                    'trans_no'=>$transaction->trans_no,
                    'trans_date'=>$transaction->trans_date,
                    'description'=>$description,
                    'is_voucher'=>false,
                    'is_single_entry'=>true,
                    'company_id'=>$transaction->company_id,
                    // 'department_id'=>$transaction->department_id,
                    // 'tags'=>$transaction->tags,
                    'total'=>$transaction->amount,
                    'transaction_type_id'=>$transaction->transaction_type_id,
                    'transaction_id'=>$transaction->id,
                    'created_by'=>$transaction->created_by
                ]);
                $jcounter->save();
                $check = false;
            }
        }while($check);
        $debit = 0;
        $credit = 0;
        if($transaction->trans_type=='receipt'){
            $debit = $transaction->amount;
            $credit = 0;
        }else if($transaction->trans_type=='payment'){
            $credit = $transaction->amount;
            $debit = 0;
        }
        JournalDetail::create([
            'trans_date'=>$transaction->trans_date,
            'sequence'=>0,
            'account_id'=>$transaction->account_id,
            'description'=>$description,
            'department_id'=>$transaction->department_id,
            'is_locked'=>true,
            // 'tags'=>$transaction->tags,
            'debit'=>$debit,
            'credit'=>$credit,
            'journal_id'=>$journal->id,
            'created_by'=>$journal->created_by
        ]);
        foreach($transaction->details as $i =>$detail){
            $debit = 0;
            $credit = 0;
            if($transaction->trans_type=='receipt'){
                $credit = $detail->amount;
                $debit = 0;
            }else if($transaction->trans_type=='payment'){
                $debit = $detail->amount;
                $credit = 0;
            }
            JournalDetail::create([
                'trans_date'=>$transaction->trans_date,
                'sequence'=>$detail->sequence+1,
                'account_id'=>$detail->account_id,
                'description'=>$detail->description,
                'department_id'=>$transaction->department_id,
                'tags'=>$detail->tags,
                'debit'=>$debit,
                'credit'=>$credit,
                'journal_id'=>$journal->id,
                'created_by'=>$journal->created_by
            ]);
        }
        $reference =[
            'id'=>$journal->id,
            'Transaction No.'=>$journal->trans_no,
            'Total'=>$journal->total
        ];
        add_log('journals', 'create', json_encode($reference));
        return $journal;
    }
}
