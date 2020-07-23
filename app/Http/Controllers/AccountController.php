<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\AccountResource;
use App\Http\Resources\JournalDetailResource;
use App\Exceptions\ApiValidationException;
use App\Account;
use App\AccountType;
use App\Balance;
use App\JournalDetail;
use App\Department;
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
        $account_parent = \App\Account::where('tree_level', '<',0)->where('company_id', $company->id)->orderBy('sequence')->get();
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
        $account_parent = \App\Account::where('tree_level', 0)->where('company_id', $company->id)->orderBy('sequence')->get();
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
                    $sequence = $parent->sequence.'-'.$data['account_no'];
                }
            }else{
                $sequence = $data['account_type_id'].'-'.$data['account_no'];
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
        $reference =[
            'id'=>$account->id,
            'Account No.'=>$account->account_no,
            'Account Name'=>$account->account_name,
        ];
        add_log('accounts', 'create', json_encode($reference));
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
        add_log('accounts', 'import', json_encode(['name'=>'Chart of Account File', 'url'=>url_file($filename)]));
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
        $old_account_name = $account->account_name;
        $old_account_no = $account->account_no;
        $old_account_type = $account->accountType->name;

        $account->account_name = $request->account_name;
        $account->account_no = $request->account_no;

        if($account->tree_level==0 && $account->account_type_id!=$request->account_type_id){
            $account->account_type_id = $request->account_type_id;
            //ubah semua tipe akun childrennya
            $this->changeAccountType($account);
        }
        $account->save();
        $reference =[
            'before'=>[
                'id'=>$account->id,
                'Account No.'=>$old_account_no,
                'Account Name'=>$old_account_name,
            ],
            'after'=>[
                'id'=>$account->id,
                'Account No.'=>$account->account_no,
                'Account Name'=>$account->account_name
            ]
        ];
        add_log('accounts', 'edit', json_encode($reference));
        return redirect()->route('accounts.index')->with('success', 'Perubahan akun telah disimpan');
    }
    private function changeAccountType($parent){
        $accounts = Account::where('account_parent_id', $parent->id)->get();
        foreach($accounts as $account){
            $account->account_type_id = $parent->account_type_id;
            $account->save();
            $this->changeAccountType($account);
        }
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
        $company = $user->activeCompany();
        $balance_date = $company->accounting_start_date;
        $department_id = $request->department_id;
        $account_type_id = $request->query('account_type_id', 1);
        $budget_year = $request->query('budget_year', date('Y'));
        $account = \DB::table('accounts')
        ->leftJoin('account_types', 'account_types.id', '=', 'account_type_id')
        ->leftJoin('budgets', function($join)use($budget_year, $department_id){
            if(empty($department_id)){
                $join->on('budgets.account_id', '=', 'accounts.id')->where('budget_year', $budget_year)->whereNull('department_id');
            }else{
                $join->on('budgets.account_id', '=', 'accounts.id')->where('budget_year', $budget_year)->where('department_id', $department_id);
            }
        })
        ->selectRaw('sequence, accounts.id, account_name, account_no, account_parent_id,
        account_types.id as account_type_id, account_types.name as account_type_name, has_children, tree_level, jan, feb, mar, apr, may, jun, jul, aug, sep, `oct`, nov, `dec`, total')
        ->where('accounts.company_id', $company->id);//->where('account_type_id','<',12);

        if(!empty($account_type_id)){
            $account = $account->where('account_type_id', $account_type_id);
        }
        $account = $account->orderBy('account_type_id', 'asc');
        $account = $account->orderBy('sequence', 'asc');
        $accounts = $account->get();
        $departments = Department::where('company_id', $company->id)->get();
        $account_types = AccountType::orderBy('id')->get();
        return view('account.budget', compact('accounts', 'budget_year', 'departments', 'account_types', 'account_type_id'));
    }
    public function openingBalance(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();
        $balance_date = $company->accounting_start_date;
        $department_id = $request->department_id;
        $account_type_id = $request->account_type_id;
        $account = \DB::table('accounts')
        ->leftJoin('account_types', 'account_types.id', '=', 'account_type_id')
        ->leftJoin('balances', function($join)use($department_id){
            if(empty($department_id)){
                $join->on('balances.account_id', '=', 'accounts.id')->whereNull('department_id');
            }else{
                $join->on('balances.account_id', '=', 'accounts.id')->where('department_id', $department_id);
            }
        })
        ->selectRaw('sequence, accounts.id, account_name, account_no, account_parent_id, account_types.id as account_type_id, account_types.name as account_type_name, has_children, tree_level, balance')
        ->where('accounts.company_id', $company->id);//->where('account_type_id','<',12);



        if(!empty($account_type_id)){
            $account = $account->where('account_type_id', $account_type_id);
        }
        $account = $account->orderBy('account_type_id', 'asc');
        $account = $account->orderBy('sequence', 'asc');
        $accounts = $account->get();
        $departments = Department::where('company_id', $company->id)->get();
        $account_types = AccountType::orderBy('id')->get();
        return view('account.balance', compact('accounts', 'balance_date', 'departments', 'account_types'));
    }

    public function saveOpeningBalance(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();
        $period = $company->getPeriod();
        $data = $request->all();
        $opening_balance = array();
        foreach($data['balance'] as $account_id =>$balance){
            if($balance!=null){
                \DB::table('balances')->updateOrInsert([
                    'company_id' => $company->id,
                    'department_id' => $request->department_id??null,
                    'account_id' => $account_id,
                ],['balance' => parse_number($balance),
                    'created_by'=>$user->id,
                    'created_at'=>date('Y-m-d H:i:s')
                ]);
            }
        }
        \DB::table('balances')->insert($opening_balance);
        $params = [
        ];
        if(!empty($request->account_type_id)){
            $params = [
                'account_type_id'=>$data['account_type_id']
            ];
        }
        if(!empty($request->department_id)){
            $params['department_id']=$request->department_id;
        }
        return redirect()->route('accounts.opening_balance', $params)->with('success', 'Saldo awal telah disimpan.');
    }
    public function saveBudget(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();
        $period = $company->getPeriod();
        $data = $request->all();
        $budgets = array();
        // dd($data);
        foreach($data['budget'] as $account_id =>$account_budgets){
            $budget_values=array(
                'created_by'=>$user->id,
                'updated_by'=>$user->id,
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s'),
            );
            $total = 0;
            foreach($account_budgets as $month => $budget){
                $budget_values[$month] = parse_number($budget);
                if($month!='total'){
                    $total += $budget_values[$month];
                }
            }
            $budget_values['total']=$total;
            \DB::table('budgets')->updateOrInsert([
                'company_id' => $company->id,
                'department_id' => $request->department_id??null,
                'account_id' => $account_id,
                'budget_year' => $data['budget_year'],
            ],$budget_values);
        }
        \DB::table('balances')->insert($budgets);
        $params = [
            'budget_year'=>$data['budget_year'],
            'account_type_id'=>$data['account_type_id']
        ];
        if(!empty($department_id)){
            $params['department_id']=$department_id;
        }
        return redirect()->route('accounts.budgets', $params)->with('success', 'Saldo awal telah disimpan.');
    }
}
