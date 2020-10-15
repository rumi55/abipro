<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\JournalResource;
use App\Exceptions\ApiValidationException;
use App\Counter;
use App\Numbering;
use App\Journal;
use App\JournalDetail;
use App\TransactionType;
use Auth;
use Validator;
use PDF;

class JournalController extends Controller
{
    public function getAll(Request $request){
        return $this->query($request);
    }
    public function getAllJournal(Request $request){
        return $this->query($request);
    }

    public function getAllVoucher(Request $request){
        return $this->query($request, true);
    }

    public function query(Request $request, $is_voucher=false){
        $company_id = Auth::user()->activeCompany()->id;
        $journal = \DB::table('journals')->where('company_id', '=', $company_id)
        ->selectRaw('id, trans_no as text')
        ->where('is_voucher', $is_voucher);
        return $journal->get();
    }
    public function get($id){
        $id = decode($id);
        $journal = Journal::findOrFail($id);
        $prev=Journal::where('company_id', $journal->company_id)
        ->where('id','<', $journal->id)->orderBy('id', 'desc')->first();
        $next=Journal::where('company_id', $journal->company_id)
        ->where('id','>', $journal->id)->orderBy('id', 'asc')->first();
        return (new JournalResource($journal))
        ->additional(['meta' => [
            'previous' => $prev!=null?encode($prev->id):'',
            'next' => $next!=null?encode($next->id):'',
        ]]);
    }

    public function view($id){
        // $id = decode($id);
        $journal = Journal::findOrFail($id);
        if($journal->is_voucher && $journal->is_processed==0){
            if(!empty($journal->transaction_id)){
                return redirect()->route('vouchers.view.transaction', ['id'=>$journal->transaction_id]);
            }
            $prev=Journal::where('company_id', $journal->company_id)->where('is_voucher', 1)->where('is_processed', 0)
            ->where('id','<', $journal->id)->orderBy('id', 'desc')->first();
            $next=Journal::where('company_id', $journal->company_id)->where('is_voucher', 1)->where('is_processed', 0)
            ->where('id','>', $journal->id)->orderBy('id', 'asc')->first();
        }else{
            $prev=Journal::where('company_id', $journal->company_id)
            ->where('id','<', $journal->id)->orderBy('id', 'desc')->first();
            $next=Journal::where('company_id', $journal->company_id)
            ->where('id','>', $journal->id)->orderBy('id', 'asc')->first();
        }
        $prev_id = $prev!=null?$prev->id:'';
        $next_id = $next!=null?$next->id:'';
        // $journal = new JournalResource($journal);
        return view('journal.view', compact('journal', 'next_id', 'prev_id'));
    }

    public function createJournal(Request $request){
        return $this->create(0);
    }
    public function duplicateJournal($id){
        return $this->duplicate($id, 0);
    }
    public function createVoucher(Request $request){
        return $this->create(1);
    }
    public function duplicateVoucher($id){
        return $this->duplicate($id, 1);
    }

    public function create($is_voucher){
        $company_id = \Auth::user()->activeCompany()->id;
        $journal = new Journal;
        $journal->is_voucher = $is_voucher;
        $accounts = \App\Account::where('company_id', $company_id)
        ->where('has_children', false)->orderBy('account_type_id')->get();
        $departments = \App\Department::where('company_id', $company_id)->get();
        $tags = \App\Tag::where('company_id', $company_id)->orderBy('group')->orderBy('item_id')->get();
        $numberings = \App\Numbering::where('company_id', $company_id)
        ->where('transaction_type_id', TransactionType::JOURNAL)->get();
        $contacts = \App\Contact::where('company_id', $company_id)->orderBy('name')->get();
        $mode = 'create';
        return view('journal.form', compact('journal', 'mode', 'accounts', 'departments', 'numberings', 'tags', 'contacts'));
    }
    public function duplicate($id, $is_voucher){
        $company_id = \Auth::user()->activeCompany()->id;
        $journal = Journal::findOrFail($id);
        $journal->is_voucher = $is_voucher;
        //redirect jika single entry
        if($journal->is_voucher==1 && $journal->is_processed==0 && !empty($journal->transaction_id)){
            $trans = \App\Transaction::findOrFail($journal->transaction_id);
            return redirect()->route('vouchers.create.single.duplicate', ['type'=>$trans->trans_type, 'id'=>$trans->id]);
        }
        $accounts = \App\Account::where('company_id', $company_id)
        ->where('has_children', false)->orderBy('account_type_id')->get();
        $departments = \App\Department::where('company_id', $company_id)->get();
        $tags = \App\Tag::where('company_id', $company_id)->orderBy('group')->orderBy('item_id')->get();
        $numberings = \App\Numbering::where('company_id', $company_id)
        ->where('transaction_type_id', TransactionType::JOURNAL)->get();
        $contacts = \App\Contact::where('company_id', $company_id)->orderBy('name')->get();
        $journal->numbering_id = null;
        $mode = 'create';
        return view('journal.form', compact('journal', 'mode', 'accounts', 'departments', 'numberings', 'tags', 'contacts'));
    }
    public function edit($id){
        $company_id = \Auth::user()->activeCompany()->id;
        $journal = Journal::findOrFail($id);
        if($journal->is_voucher==1 && $journal->is_processed==0 && !empty($journal->transaction_id)){
            $trans = \App\Transaction::findOrFail($journal->transaction_id);
            return redirect()->route('vouchers.edit.single', ['id'=>$journal->transaction_id, 'type'=>$trans->trans_type]);
        }
        $accounts = \App\Account::where('company_id', $company_id)
        ->where('has_children', false)->orderBy('account_type_id')->get();
        $departments = \App\Department::where('company_id', $company_id)->get();
        $tags = \App\Tag::where('company_id', $company_id)->orderBy('group')->orderBy('item_id')->get();
        $numberings = \App\Numbering::where('company_id', $company_id)
        ->where('transaction_type_id', TransactionType::JOURNAL)->get();
        $contacts = \App\Contact::where('company_id', $company_id)->orderBy('name')->get();
        $mode = 'edit';
        return view('journal.form', compact('journal', 'mode', 'accounts', 'departments', 'numberings', 'tags', 'contacts'));
    }

    public function report(Request $request, $id){
        $output = $request->query('output', 'pdf');
        $journal = Journal::findOrFail($id);
        $title = $journal->is_voucher==1?'Voucher':'Jurnal';
        $view = 'report.journal.single';
        $data = ['data'=>$journal, 'title'=>$title, 'report'=>'print_journal', 'view'=>$view];
        if($output=='excel'){
            header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=journals.xls");
            return view('report.single.pdf', $data);
        }
        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('report.pdf', $data);
        return $pdf->download('journal.pdf');
    }
    public function voucher(Request $request, $id){
        $output = $request->query('output', 'pdf');
        $journal = Journal::findOrFail($id);
        $title = $journal->is_voucher==1?'Voucher':'Jurnal';
        $view = 'report.single.voucher';
        $data = ['data'=>$journal, 'title'=>$title, 'report'=>'print_journal', 'view'=>$view];
        if($output=='excel'){
            header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=journals.xls");
            return view('report.single.pdf', $data);
        }
        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('report.single.pdf', $data);
        return $pdf->download('voucher.pdf');
    }
    public function receipt(Request $request, $id){
        $output = $request->query('output', 'pdf');
        $journal = Journal::findOrFail($id);
        if(!$journal->is_voucher){
            abort(404);
        }
        if(!empty($journal->transaction_id)){
            $voucher = \App\Transaction::find($journal->transaction_id);
            $type = 'transaction';
        }else{
            $type = 'voucher';
            $voucher = $journal;
        }
        $title = 'Kuitansi';
        $view = 'report.single.receipt';
        $data = ['data'=>$voucher, 'title'=>$title, 'type'=>$type,'report'=>'print_receipt', 'view'=>$view];
        if($output=='excel'){
            header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=journals.xls");
            return view('report.single.pdf', $data);
        }
        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('report.single.pdf', $data);
        return $pdf->download('receipt.pdf');

    }

    public function save(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $company_id = company('id');
        // dd($data);
        $manual = !empty($request->manual)&&$request->manual==1?true:false;

        $rules = [
            'trans_date' => 'required|date_format:d-m-Y',
            'description' => 'nullable|min:1|max:255',
            'total_debit' => 'same:total_credit',
            'total_credit' => 'same:total_debit',
            'detail.*.account_id'=>'required',
            'detail.*.description'=>'required',
            // 'detail_department_id'=>'required',
            'detail.*.debit'=>'required',
            'detail.*.credit'=>'required',
        ];
        if($manual){
            $rules['trans_no_manual'] = "required|unique:journals,trans_no,NULL,id,company_id,$company_id";
        }else{
            $rules['numbering_id'] = 'required|exists:numberings,id';
        }
        $attr = [
            'trans_no_manual' => trans('Transaction Number'),
            'trans_no_auto' => trans('Transaction Number'),
            'numbering_id' => trans('Group Transaction'),
            'trans_date' => trans('Transaction Date'),
            'description' => trans('Description'),
            'total_debit' => trans('Total Debit'),
            'total_credit' => trans('Total Credit'),
            'detail.*.account_id'=>trans('Account'),
            'detail.*.description'=>trans('Description'),
            'detail.*.department_id'=>trans('Departement'),
            'detail.*.debit'=>trans('Debit'),
            'detail.*.credit'=>trans('Credit'),
        ];

        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $is_voucher = $request->is_voucher;

        $trans_date = fdate($request->trans_date, 'Y-m-d');
        $total = $request->total;
        $trans_no = $manual?$request->trans_no_manual:$request->trans_no_auto;

        try{
            \DB::beginTransaction();
            if($manual==false){
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
                    $c = Journal::where('trans_no', $trans_no)->where('company_id', $company_id)->count();
                    if($c==0){
                        $journal = Journal::create([
                            'journal_id'=>$trans_no,
                            'trans_no'=>$trans_no,
                            'trans_date'=>$trans_date,
                            'description'=>$request->description,
                            'company_id'=>$company_id,
                            'contact_id'=>$request->contact_id??null,
                            'is_voucher'=>$is_voucher,
                            'is_processed'=>$is_voucher?0:1,
                            'status'=>$request->status,
                            'numbering_id'=>$request->numbering_id,
                            'total'=>$total,
                            'transaction_type_id'=>TransactionType::JOURNAL,
                            'created_by'=>$user->id
                        ]);
                        $counter->save();
                        $check = false;
                    }
                    $counter->getNumber();
                    $trans_no = $counter->last_number;
                }while($check);
            }else{
                $journal = Journal::create([
                    'journal_id'=>$trans_no,
                    'trans_no'=>$trans_no,
                    'trans_date'=>$trans_date,
                    'description'=>$request->description,
                    'numbering_id'=>$request->numbering_id,
                    'company_id'=>$company_id,
                    'contact_id'=>$request->contact_id??null,
                    'is_voucher'=>$is_voucher,
                    'is_processed'=>$is_voucher?0:1,
                    'status'=>$request->status,
                    'numbering_id'=>$request->numbering_id,
                    'total'=>$total,
                    'transaction_type_id'=>TransactionType::JOURNAL,
                    'created_by'=>$user->id
                ]);
            }
            $i=0;
            foreach($request->detail as $i=> $detail){
                $account_id = $detail['account_id'];
                $department_id = isset($detail['department_id'])?$detail['department_id']:null;
                $description = isset($detail['description'])?$detail['description']:null;
                $tags = isset($detail['tags'])?$detail['tags']:null;
                $debit = $detail['debit'];
                $credit = $detail['credit'];
                JournalDetail::create([
                    'sequence'=>$i++,
                    'trans_date'=>$trans_date,
                    'account_id'=>$account_id,
                    'description'=>$description,
                    'department_id'=>$department_id,
                    'tags'=>$tags,
                    'debit'=>parse_number($debit),
                    'credit'=>parse_number($credit),
                    'journal_id'=>$journal->id,
                    'created_by'=>$user->id
                ]);
            }

            \DB::commit();
            $reference =[
                'id'=>$journal->id,
                'Transaction No.'=>$journal->trans_no,
                'Total'=>$journal->total
            ];
            add_log($is_voucher?'vouchers':'journals', 'create', json_encode($reference));
            return redirect()->route('journals.view', $journal->id)->with('success', trans(':attr have been created successfully.',['attr'=>trans($journal->is_voucher?'Voucher':'Journal')]));
        }catch(Exception $e){
            \DB::rollback();
            return redirect()->route('journals.view', $journal->id)->with('error', 'Terjadi kesalahan');
        }
    }

    public function update(Request $request, $id){
        $data = $request->all();
        $company_id = company('id');
        $rules = [
            'trans_date' => 'required|date_format:d-m-Y',
            'description' => 'nullable|min:1|max:255',
            'total_debit' => 'same:total_credit',
            'total_credit' => 'same:total_debit',
            'detail.*.account_id'=>'required',
            'detail.*.description'=>'required',
            'detail.*.debit'=>'required',
            'detail.*.credit'=>'required',
        ];
        $attr = [
            'trans_date' => trans('Date'),
            'description' => trans('Description'),
            'total_debit' => trans('Total Debit'),
            'total_credit' => trans('Total Credit'),
            'detail.*.account_id'=>trans('Account'),
            'detail.*.description'=>trans('Description'),
            'detail.*.department_id'=>trans('Departement'),
            'detail.*.debit'=>trans('Debit'),
            'detail.*.credit'=>trans('Credit'),
        ];

        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $journal = Journal::findOrFail($id);
        if($company_id!=$journal->company_id){
            abort(404);
        }
        $old_total = $journal->total;
        $user = Auth::user();
        $trans_date = fdate($request->trans_date, 'Y-m-d');

        try{
            \DB::beginTransaction();
            $journal->trans_date = $trans_date;
            $journal->description = $request->description;
            $journal->numbering_id = $request->numbering_id;
            $journal->contact_id = $request->contact_id??null;
            $journal->total = $request->total;
            $journal->updated_by = $user->id;
            if(!empty($request->status)){
                $journal->status = $request->status;
            }
            $journal->update();
            //cek id
            $old_details = array();

            foreach($journal->details as $d){
                $old_details[$d->id] = $d;
            }
            $i=0;
            foreach($request->detail as $detail){
                $account_id = $detail['account_id'];
                $detail_id = isset($detail['id'])?$detail['id']:null;
                $department_id = isset($detail['department_id'])?$detail['department_id']:null;
                $description = isset($detail['description'])?$detail['description']:null;
                $tags = isset($detail['tags'])?$detail['tags']:null;
                $debit = $detail['debit'];
                $credit = $detail['credit'];
                if(empty($detail_id)){
                    JournalDetail::updateOrcreate([
                        'account_id'=>$account_id,
                        'sequence'=>$i,
                        'trans_date'=>$trans_date,
                        'description'=>$description,
                        'department_id'=>$department_id,
                        'tags'=>$tags,
                        'debit'=>parse_number($debit),
                        'credit'=>parse_number($credit),
                        'journal_id'=>$journal->id,
                        'created_by'=>$user->id
                    ]);
                }else{
                    $jid = $detail_id;
                    $jdetail = JournalDetail::findOrFail($jid);
                    $jdetail->sequence=$i++;
                    $jdetail->trans_date=$trans_date;
                    $jdetail->account_id=$account_id;
                    $jdetail->description=$description;
                    $jdetail->department_id=$department_id;
                    $jdetail->tags=$tags;
                    $jdetail->debit=parse_number($debit);
                    $jdetail->credit=parse_number($credit);
                    $jdetail->journal_id=$journal->id;
                    $jdetail->updated_by=$user->id;
                    $jdetail->update();
                    if(array_key_exists($jid, $old_details)){
                       unset($old_details[$jid]);
                    }
                }
            }
            foreach($old_details as $d){
                $d->delete();
            }
            \DB::commit();
        }catch(Exception $e){
            \DB::rollback();
        }

        $reference =[
            'before'=>[
                'id'=>$journal->id,
                'Transaction No.'=>$journal->trans_no,
                'Total'=>$old_total
            ],
            'after'=>[
                'id'=>$journal->id,
                'Transaction No.'=>$journal->trans_no,
                'Total'=>$journal->total
            ]
        ];
        add_log($journal->is_voucher?'vouchers':'journals', 'edit', json_encode($reference));
        return redirect()->route('journals.view', $journal->id)->with('success', trans(':attr have been saved successfully.',['attr'=>trans($journal->is_voucher?'Voucher':'Journal')]));
    }
    public function delete($id)
    {
        $journal = Journal::findOrFail($id);
        if($journal->is_locked){
            return redirect()->route('dcru.index', 'journals')->with('success', trans("Journal is locked, cannot be deleted."));
        }
        $voucher = 'Journal';
        $vouchers = 'journals';
        if($journal->is_voucher && $journal->is_processed==0){
            $voucher = 'Voucher';
            $vouchers = 'vouchers';
            if(!empty($journal->transaction_id)){
                $trans = \App\Transaction::find($journal->transaction_id);
                if($trans!=null){
                    $trans->delete();
                }
            }
        }
        $reference =[
            'id'=>$journal->id,
            'Transaction No.'=>$journal->trans_no,
            'Total'=>$journal->total
        ];
        $journal->delete();
        add_log($vouchers, 'delete', json_encode($reference));
        return redirect()->route('dcru.index', $vouchers)->with('success', trans(":attr has been deleted.", ['attr'=>$voucher]));
    }
    public function deleteBatch(Request $request){
        $id = $request->id;
        foreach($id as $i){
            $journal = Journal::find($i);
            if($journal!=null){
                if($journal->is_voucher && !empty($journal->transaction_id)){
                    $trans = \App\Transaction::find($journal->transaction_id);
                    if($trans!=null){
                        $trans->delete();
                    }
                }
                $journal->delete();
            }
        }
        return redirect()->back()->with('success', 'Data deleted');
    }

    public function lockJournal(Request $request, $id){
        $id = decode($id);
        $journal = Journal::findOrFail($id);
        $journal->is_locked=!$journal->is_locked;
        $journal->save();
        return redirect()->back();
    }

    public function lockJournalBatch(Request $request){
        $locked = isset($request->locked)?$request->locked:false;
        $id = $request->id;
        $ids = array();
        foreach($id as $i){
            $ids[] = decode($i);
        }
        Journal::whereIn('id', $ids)->update(['is_locked'=>$locked]);
        return redirect()->back();
    }

    public function toJournal($id){
        $journal = Journal::findOrFail($id);
        $journal->is_processed=true;
        $journal->save();
        return redirect()->route('dcru.index', 'journals')->with('success', __('Voucher have been transfered to journal successfully.'));
    }

    public function toVoucher($id){
        $journal = Journal::findOrFail($id);
        if($journal->is_voucher){
            $journal->is_processed = 0;
            $journal->save();
            return redirect()->route('dcru.index', 'vouchers');
        }else{
            return redirect()->back()->with('error', 'Journal is not processed from voucher before.');
        }
    }

    public function toJournalBatch(Request $request){
        $id = $request->id;
        Journal::whereIn('id', $id)->where('is_voucher', true)->where('status', 'approved')->update(['is_processed'=>true]);
        return redirect()->back()->with('success', __('Voucher have been transfered to journal successfully.'));
    }

    public function toVoucherBatch(Request $request){
        $id = $request->id;
        Journal::whereIn('id', $id)->update(['is_processed'=>false, 'is_voucher'=>true]);
        return redirect()->back();
    }


    public function approve(Request $request, $id){
        $company_id = company('id');
        $user = \Auth::user();

        $journal = Journal::findOrFail($id);
        if($journal->company_id!=$company_id || !(in_array($request->status, ['approved', 'rejected', 'submitted'])) ){
            abort(401);
        }
        if($request->status=='submitted' && $user->id!=$journal->created_by){
            abort(401);
        }
        $journal->status = $request->status;
        $journal->approved_at = date('Y-m-d H:i:s');
        $journal->approved_by = user('id');
        $journal->rejection_note = $request->rejection_note;
        $journal->update();
        if(!empty($journal->transaction_id) && $journal->is_voucher && $journal->is_processed==false){
            $transaction = \App\Transaction::find($journal->transaction_id);
            if($transaction !=null){
                $transaction->status = $request->status;
                $transaction->approved_at = date('Y-m-d H:i:s');
                $transaction->approved_by = user('id');
                $transaction->rejection_note = $request->rejection_note;
                $transaction->update();
            }
        }
        $msg = ''; $msg_en='';
        if($journal->status=='approved'){
            //$this->addToJournal($transaction);
            $msg = $user->name.' menyetujui voucher #'.$journal->trans_no;
            $msg_en = $user->name.' approved voucher #'.$journal->trans_no;
        }else
        if($journal->status=='rejected'){
            $msg = $user->name.' menolak voucher #'.$journal->trans_no;
            $msg_en = $user->name.' rejected voucher #'.$journal->trans_no;
        }else{
            $msg = $user->name.' mengirim voucher #'.$journal->trans_no;
            $msg_en = $user->name.' submitted voucher #'.$journal->trans_no;
        }
        notify([
            'url'=>route('vouchers.view', $journal->id),
            'message_en'=>$msg_en,
            'message'=>$msg,
            'users'=>[$journal->created_by]
        ]);

        $msg = $journal->status=='submitted'?trans('Voucher submitted!'):($journal->status=='approved'?trans('Voucher approved!'):trans('Voucher rejected!'));
        return redirect()->route('vouchers.view', $journal->id)->with('success', $msg);
    }


    public function journalType(){
        $data = dcru_dt('journal_types', 'dtables');
        return view('company.journal_type.index', $data);
    }
    public function createJournalType(){
        $model = new Numbering;
        $model->counter_reset = 'y';
        $model->counter_digit = 4;
        $model->counter_start = 1;
        $mode = 'create';
        return view('company.journal_type.form', compact('model', 'mode'));
    }
    public function duplicateJournalType($id){
        $model = Numbering::findOrFail($id);
        $mode = 'create';
        return view('company.journal_type.form', compact('model', 'mode'));
    }
    public function editJournalType($id){
        $model = Numbering::findOrFail($id);
        $mode = 'edit';
        return view('company.journal_type.form', compact('model', 'mode'));
    }
    public function viewJournalType($id){
        $model = Numbering::findOrFail($id);
        return view('company.journal_type.view', compact('model', 'mode'));
    }
    public function saveJournalType(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();

        $data = $request->all();
        $rules = [
            'name' => 'required|max:64|unique:numberings,name,NULL,id,company_id,'.$company->id,
            'format' => 'required|max:64|unique:numberings,format,NULL,id,company_id,'.$company->id,
            'counter_start'=>'required|',
            'counter_digit'=>'required|integer|min:2',
            'counter_start'=>'required|integer|min:0',
        ];
        $attr = [
            'name' => 'Jenis Jurnal',
            'format' => 'Format Penomoran',
            'counter_start'=>'Counter Reset',
            'counter_digit'=>'Counter Digit',
            'counter_start'=>'Counter Start',
        ];
        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = array_merge($request->all(), ['transaction_type_id'=>'journal','company_id'=>$company->id]);
        $model = Numbering::create($data);
        add_log('journal_types', 'create', json_encode(['id'=>$model->id, 'name'=>$model->name]));
        return redirect()->route('journal_types.index')->with('success', 'Jenis jurnal baru telah ditambahkan');
    }
    public function updateJournalType(Request $request, $id){
        $model = Numbering::findOrFail($id);
        $user = Auth::user();
        $company = $user->activeCompany();

        $data = $request->all();
        $rules = [
            'name' => 'required|max:64|unique:numberings,name,'.$id.',id,company_id,'.$company->id,
            'format' => 'required|max:64|unique:numberings,format,'.$id.',id,company_id,'.$company->id,
            'counter_start'=>'required|',
            'counter_digit'=>'required|integer|min:2',
            'counter_start'=>'required|integer|min:0',
        ];
        $attr = [
            'name' => 'Jenis Jurnal',
            'format' => 'Format Penomoran',
            'counter_start'=>'Counter Reset',
            'counter_digit'=>'Counter Digit',
            'counter_start'=>'Counter Start',
        ];
        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $model->name = $request->name;
        $model->format = $request->format;
        $model->transaction_type_id = 'journal';
        $model->counter_reset = $request->counter_reset;
        $model->counter_digit = $request->counter_digit;
        $model->counter_start = $request->counter_start;
        $model->save();
        add_log('journal_types', 'edit', json_encode(['id'=>$model->id, 'name'=>$model->name]));
        return redirect()->route('journal_types.index')->with('success', 'Jenis jurnal '.$model->name.' telah diperbarui');
    }
    public function deleteJournalType($id)
    {
        $model = Numbering::findOrFail($id);
        $name = $model->name;
        $model->delete();
        add_log('journal_types', 'delete', json_encode(['name'=>$name]));
        return redirect()->route('journal_types.index')->with('success', 'Jenis jurnal '.$name.' telah dihapus');
    }
    public function import()
    {
        return view('journal.import');
    }
    public function importSave(Request $request)
    {

        $data = $request->all();

        $user = Auth::user();
        $company = $user->activeCompany();
        $rules = [
            'file'=>'required',
        ];
        $attr = [
            'file'=>'File',
        ];

        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $filename = \Str::slug('import_journals_'.$request->target.'_'.date('Y m d H i s').' '.time(),'_');
        $filename = upload_file('file', $filename, 'public/files/import');
        $excel = \Excel::import(new \App\Imports\LedgerImport($company->id, $user->id), storage_path("/app/".$filename));
        add_log('journals', 'import', json_encode(['name'=>'Journal File', 'url'=>url_file($filename)]));
        return redirect()->route('dcru.index', 'journals')->with('success', 'Jurnla berhasil ditambahkan');
    }

}
