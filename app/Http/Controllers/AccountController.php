<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\AccountResource;
use App\Http\Resources\JournalDetailResource;
use App\Exceptions\ApiValidationException;
use App\Account;
use App\Balance;
use App\JournalDetail;
use App\Imports\AccountsImport;
use Auth;
use Validator;
use Str;
use Excel;

class AccountController extends Controller
{

    public function index(Request $request){
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $account = Account::where('company_id', $company_id);
        $account = $account->orderBy('account_type_id', 'asc');
        $account = $account->orderBy('sequence', 'asc');
        $accounts = $account->get();
        return view('account.index', compact('accounts'));
    }

    public function view($id){ 
        $account = Account::findOrFail($id);
        $data = dcru_dt('transactions', 'dtables');
        $data['account'] = $account;
        return view('account.view', $data);
    }
    
    
    public function create(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();
        $account = new Account;
        $mode= 'create';
        if(!empty($request->parent_id)){
            $parent = Account::findOrFail($request->parent_id);
            $account->account_type_id = $parent->account_type_id;
            $account->account_parent_id = $parent->id;
            $mode= 'add_child';
        }
        $account_types = \App\AccountType::orderBy('id')->get();
        $account_parent = \App\Account::where('company_id', $company->id)->orderBy('sequence')->get();
        return view('account.form', compact('account', 'mode', 'account_types', 'account_parent'));
    }
    public function import(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();
        $account = new Account;
        $mode= 'create';
        if(!empty($request->parent_id)){
            $parent = Account::findOrFail($request->parent_id);
            $account->account_type_id = $parent->account_type_id;
            $account->account_parent_id = $parent->id;
            $mode= 'add_child';
        }
        $account_types = \App\AccountType::orderBy('id')->get();
        $account_parent = \App\Account::where('company_id', $company->id)->orderBy('sequence')->get();
        return view('account.import', compact('account', 'mode', 'account_types', 'account_parent'));
    }
    public function edit(Request $request, $id){
        $user = Auth::user();
        $company = $user->activeCompany();
        $account = Account::findOrFail($id);
        $mode= 'edit';
        $account_types = \App\AccountType::orderBy('id')->get();
        $account_parent = \App\Account::where('company_id', $company->id)->orderBy('sequence')->get();
        return view('account.form', compact('account', 'mode', 'account_types', 'account_parent'));
    }
    public function save(Request $request){
        $data = $request->all();
        
        $user = Auth::user();
        $company = $user->activeCompany();
        $rules = [
            'account_no'=>'required|max:32|unique:accounts,account_no,NULL,id,company_id,'.$company->id,
            'account_name'=>'required|max:128|unique:accounts,account_name,NULL,id,company_id,'.$company->id,
            'account_type_id'=>'required|integer|exists:account_types,id',
            'account_parent_id'=>'nullable|integer|exists:accounts,id',
        ];
        $attr = [
            'account_no'=>trans('Account No.'),
            'account_name'=>trans('Account Name'),
            'account_type_id'=>trans('Account Type'),
            'account_parent_id'=>trans('Account Parent'),
        ];
        
        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $account_parent_id = $request->account_parent_id;
        $parent = null;
        $level = 0;
        $sequence = '';
        try{
            \DB::beginTransaction();
            if(!empty($account_parent_id)){
                $parent = Account::find($account_parent_id);
                if($parent!==null){
                    $level = $parent->tree_level+1;
                    $seq = Account::where('company_id', $company->id)
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
            }else{
                $seq = Account::where('company_id', $company->id)
                ->whereNull('account_parent_id')
                ->where('account_type_id', $data['account_type_id'])
                ->orderBy('sequence', 'desc')->first();
                if($seq==null){
                    $sequence = $data['account_type_id']*1000;
                }else{
                    $sequence = $seq->sequence+1;
                }
            }
            $account = Account::create([
                'account_name' => $data['account_name'],
                'account_no' => $data['account_no'],
                'account_type_id' => $data['account_type_id'],
                'account_parent_id' => $account_parent_id,
                'company_id'=>$company->id,
                'tree_level'=>$level,
                'sequence'=>$sequence
            ]);
            if($parent!==null){
                $parent->has_children = true;
                $parent->update();
            }
        \DB::commit();        
        }catch(Exception $e){
            \DB::rollback();
        }

        add_log('accounts', 'create', '');
        return redirect()->route('accounts.index')->with('success', 'Akun  baru berhasil ditambahkan');
    }
    public function saveImport(Request $request){
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
        $filename = Str::slug('import_data_'.$request->target.'_'.date('Y m d H i s').' '.time(),'_');
        $filename = upload_file('file', $filename, 'public/files/import');
        $excel = Excel::import(new AccountsImport($company->id, $user->id), storage_path("/app/".$filename));
        

        add_log('accounts', 'create', '');
        return redirect()->route('accounts.index')->with('success', 'Akun  baru berhasil ditambahkan');
    }
    public function update(Request $request, $id){
        $account = Account::findOrFail($id);
        $data = $request->all();
        
        $user = Auth::user();
        $company = $user->activeCompany();
        $rules = [
            'account_no'=>"required|max:32|unique:accounts,account_no,$id,id,company_id,$company->id",
            'account_name'=>"required|max:128|unique:accounts,account_name,$id,id,company_id,$company->id",
            // 'account_type_id'=>'required|integer|exists:account_types,id'
        ];
        $attr = [
            'account_no'=>trans('Account No.'),
            'account_name'=>trans('Account Name'),
            'account_type_id'=>trans('Account Type'),
            'account_parent_id'=>trans('Account Parent'),
        ];
        
        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $account->account_name = $request->account_name;
        $account->account_no = $request->account_no;
        // $account->account_type_id = $request->account_type_id;
        // $account->account_parent_id = $request->account_parent_id;
        $account->save();
        add_log('accounts', 'update', '');
        return redirect()->route('accounts.index')->with('success', 'Perubahan akun telah disimpan');
    }
    public function updateLevelChildren($account){
        $children = $account->children();
        if(count($children)>0){
            foreach($children as $child){
                $child->tree_level = $level+1;
                $child->save();
                $this->updateLevelChildren($child);
            }
        }
    }
    public function delete($id)
    {
        $account = Account::findOrFail($id);
        $parent = $account->parent;
        $account->delete();
        if($parent!==null){
            if(count($parent->children)==0){
                $parent->has_children=0;
                $parent->update();
            }
        }
        add_log('accounts', 'delete', '');
        return redirect()->route('accounts.index')->with('success', 'Akun telah dihapus.');
    }

    private function getAccountId($account){
        $account_id = array();
        if($account->has_children){
            $children = $account->children;
            foreach($children as $child){
                $ids = $this->getAccountId($child);
                $account_id = array_merge($account_id, $ids);
            }
        }else{
            $account_id[]=$account->id;
        }
        return $account_id;
    }
    public function transaction(Request $request, $id){
        $id = decode($id);
        $company = Auth::user()->activeCompany();
        $account = Account::find($id);
        $account_id =  $this->getAccountId($account);
        $details = JournalDetail::join('journals', 'journal_details.journal_id', '=', 'journals.id')
        ->whereIn('account_id', $account_id)
        ->where('company_id', $company->id);
        
        $departments = $request->query('departements');
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');
        if(isset($start_date)){
            $details= $details->whereDate('trans_date', '>=', $start_date);
        }
        if(isset($end_date)){
            $details= $details->whereDate('trans_date', '<=', $end_date);
        }
        if(isset($departments)){
            $ex = explode(',',$departments);
            $dep_id = array();
            foreach($ex as $e){
                $dep_id[] = decode($e);
            }
            $details= $details->whereIn('department_id', $dep_id);
        }
        $page_size = $request->query('page_size', $details->count());
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter');
        
        if(isset($filter)){
            foreach($filter as $column => $value){
                $details = $details->where($column,'=', $value);
            }
        }
        
        $sort = $request->query('sort');
        if(!empty($sort)){
            $sort = explode('-',$sort);
            $sort_key=$sort[0];
            $sort_order=count($sort)==2?(substr($sort[1], 0, 3)=='asc'?'asc':'desc'):'asc';
            $details = $details->orderBy($sort_key, $sort_order);
        }
        if(!empty($search)){
            $details = $details->where(function ($query) use($search){
                $query->where('journal_details.description','like', "%$search%")
                ->orWhere('journals.trans_no','like', "%$search%")
                ->orWhere('journals.trans_date','like', "%$search%");
            });
        }
        
        if(!empty($sort_key)){
            $details = $details->orderBy($sort_key, $sort_order);
        }
        if(empty($sort_key) && empty($sort)){
            $details = $details->orderBy('journals.trans_date', 'desc');
        }
        $details = $details->selectRaw("journals.trans_date, journals.trans_no, journal_details.*");
        $data = $details->paginate($page_size)->appends($request->query());
        return JournalDetailResource::collection($data);
    }

    public function budget(Request $request){
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $account = Account::where('company_id', $company_id)->where('account_type_id','>',11);
        $account = $account->orderBy('account_type_id', 'asc');
        $account = $account->orderBy('sequence', 'asc');
        $accounts = $account->get();
        return view('account.budget', compact('accounts'));
    }
    public function openingBalance(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();
        $account = Account::where('company_id', $company->id)->where('account_type_id','<',12);
        $account = $account->orderBy('account_type_id', 'asc');
        $account = $account->orderBy('sequence', 'asc');
        $accounts = $account->get();
        $balance_date = $company->accounting_start_date;
        return view('account.balance', compact('accounts', 'balance_date'));
    }

    public function saveOpeningBalance(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();
        $period = $company->getPeriod();
        $data = $request->all();
        $accounts = Account::where('company_id', $company->id)
        ->where('has_children',0)
        ->where('account_type_id','<',12)
        ->get();

        foreach($accounts as $account){
            $account->op_debit=parse_number($data['debit_'.$account->id]);
            $account->op_credit=parse_number($data['credit_'.$account->id]);
            $account->op_date=date('Y-m-d');
            $account->save();
        }   
        return redirect()->route('accounts.opening_balance')->with('success', 'Saldo awal telah disimpan.');
    }   
}