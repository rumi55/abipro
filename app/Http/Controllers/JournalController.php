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
        $prev=Journal::where('company_id', $journal->company_id)
        ->where('id','<', $journal->id)->orderBy('id', 'desc')->first();
        $next=Journal::where('company_id', $journal->company_id)
        ->where('id','>', $journal->id)->orderBy('id', 'asc')->first();
        $prev_id = $prev!=null?encode($prev->id):'';
        $next_id = $next!=null?encode($next->id):'';
        // $journal = new JournalResource($journal);
        return view('journal.view', compact('journal', 'next_id', 'prev_id'));
    }

    public function createJournal(Request $request){
        return $this->create(0);
    }
    public function createVoucher(Request $request){
        return $this->create(1);
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
    public function duplicate($id){
        $company_id = \Auth::user()->activeCompany()->id;
        $journal = Journal::findOrFail($id);

        //redirect jika single entry
        if($journal->is_voucher==1 && $journal->is_single_entry==1){
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
        $mode = 'create';
        return view('journal.form', compact('journal', 'mode', 'accounts', 'departments', 'numberings', 'tags', 'contacts'));
    }
    public function edit($id){
        $company_id = \Auth::user()->activeCompany()->id;
        $journal = Journal::findOrFail($id);
        if($journal->is_voucher==1 && $journal->is_single_entry==1){
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
        $output = $request->query('output', 'html');
        $journal = Journal::findOrFail($id);
        $title = $journal->is_voucher==1?'Voucher':'Jurnal';
        $view = 'report.journal.single';
        $data = ['data'=>$journal, 'title'=>$title, 'report'=>'print_journal', 'view'=>$view];
        if($output=='pdf'){
            $pdf = app('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->loadView('report.pdf', $data);
            return $pdf->download('journal.pdf');
        }elseif($output=='print'){
            return view('report.print', $data);
        }
        return view('report.viewer', $data);
    }
    public function receipt(Request $request, $id){
        $output = $request->query('output', 'html');
        $voucher = Journal::findOrFail($id);
        if(!$voucher->is_voucher){
            abort(404);
        }
        $title = 'Kuitansi';
        $view = 'transaction.receipt';
        $data = ['data'=>$voucher, 'title'=>$title, 'report'=>'print_receipt', 'view'=>$view];
        if($output=='pdf'){
            $pdf = app('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->loadView('report.pdf', $data);
            return $pdf->download('receipt.pdf');
        }elseif($output=='print'){
            return view('report.print', $data);
        }
        return view('report.viewer', $data);
    }

    public function save(Request $request){
        $data = $request->all();
        $user = Auth::user();
        $company_id = company('id');
        // dd($data);
        $rules = [
            'trans_no' => "required|unique:journals,trans_no,NULL,id,company_id,$company_id",
            'numbering_id' => 'required',
            'trans_date' => 'required|date_format:d-m-Y',
            'description' => 'required',
            'total_debit' => 'same:total_credit',
            'total_credit' => 'same:total_debit',
            'detail.*.account_id'=>'required',
            'detail.*.description'=>'required',
            // 'detail_department_id'=>'required',
            'detail.*.debit'=>'required',
            'detail.*.credit'=>'required',
        ];
        $attr = [
            'trans_no' => trans('Number'),
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

        $is_voucher = $request->is_voucher;

        $trans_date = fdate($request->trans_date, 'Y-m-d');
        $total = parse_number($request->total);
        try{
            \DB::beginTransaction();
            $auto = empty($request->manual)?true:false;
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
                    $c = Journal::where('trans_no', $trans_no)->where('company_id', $company_id)->count();

                    if($c==0){
                        $journal = Journal::create([
                            'journal_id'=>$trans_no,
                            'trans_no'=>$trans_no,
                            'trans_date'=>$trans_date,
                            'description'=>$request->description,
                            'company_id'=>$company_id,
                            // 'contact_id'=>$request->contact_id,
                            'is_voucher'=>$is_voucher,
                            'numbering_id'=>$request->numbering_id,
                            'total'=>$total,
                            'transaction_type_id'=>TransactionType::JOURNAL,
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
                    'numbering_id'=>$request->numbering_id,
                    'company_id'=>$company_id,
                    'contact_id'=>$request->contact_id,
                    'is_voucher'=>$is_voucher,
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
        }catch(Exception $e){
            \DB::rollback();
        }
        $reference =[
            'id'=>$journal->id,
            'Transaction No.'=>$journal->trans_no,
            'Total'=>$journal->total
        ];

        add_log($is_voucher?'vouchers':'journals', 'create', json_encode($reference));
        return redirect()->route('journals.view', $journal->id)->with('success', 'Jurnal berhasil dibuat');
    }

    public function update(Request $request, $id){
        $data = $request->all();

        $company_id = company('id');
        // dd($data);
        $rules = [
            'trans_no' => "required|unique:journals,trans_no,$id,id,company_id,$company_id",
            'numbering_id' => 'required',
            'trans_date' => 'required|date_format:d-m-Y',
            'description' => 'required',
            'total_debit' => 'same:total_credit',
            'total_credit' => 'same:total_debit',
            'detail.*.account_id'=>'required',
            'detail.*.description'=>'required',
            // 'detail_department_id'=>'required',
            'detail.*.debit'=>'required',
            'detail.*.credit'=>'required',
        ];
        $attr = [
            'trans_no' => trans('Number'),
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
        $old_total = $journal->total;
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $trans_date = fdate($request->trans_date, 'Y-m-d');

        $journal->trans_date = $trans_date;
        $journal->description = $request->description;
        $journal->numbering_id = $request->numbering_id;
        // $journal->contact_id = $request->contact_id;
        $journal->total = parse_number($request->total);
        $journal->updated_by = $user->id;

        try{
            \DB::beginTransaction();
            $auto = empty($request->manual)?true:false;
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
        return redirect()->route('journals.view', $journal->id)->with('success', 'Jurnal berhasil dibuat');
    }
    public function patch(Request $request){
        $id = $request->id;
        $ids = array();
        foreach($id as $i){
            $ids[] = decode($i);
        }
        validate($request->all(), [
            'name' => 'required',
            'value' => 'required',
        ]);
        $name = $request->name;
        $value = $request->value;
        Journal::whereIn('id', $ids)->update([$name=>$value]);
        return JournalResource::collection(Journal::whereIn('id', $ids)->get());
    }

    public function delete($id)
    {
        $journal = Journal::findOrFail($id);
        $reference =[
            'id'=>$journal->id,
            'Transaction No.'=>$journal->trans_no,
            'Total'=>$journal->total
        ];
        $trans_no = $journal->trans_no;
        $journal->delete();
        add_log('journals', 'delete', json_encode($reference));
        return redirect()->route('dcru.index', 'journals')->with('success', "Jurnal #$trans_no telah dihapus");
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
        $id = decode($id);
        $journal = Journal::findOrFail($id);
        $journal->is_voucher=false;
        $journal->save();
        return redirect()->back();
    }

    public function toVoucher($id){
        $id = decode($id);
        $journal = Journal::findOrFail($id);
        $journal->is_voucher=true;
        $journal->save();
        return redirect()->back();
    }

    public function toJournalBatch(Request $request){
        $id = $request->id;
        $ids = array();
        foreach($id as $i){
            $ids[] = decode($i);
        }
        Journal::whereIn('id', $ids)->update(['is_voucher'=>false]);
        return redirect()->back();
    }

    public function toVoucherBatch(Request $request){
        $id = $request->id;
        $ids = array();
        foreach($id as $i){
            $ids[] = decode($i);
        }
        Journal::whereIn('id', $ids)->update(['is_voucher'=>true]);
        return redirect()->back();
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
