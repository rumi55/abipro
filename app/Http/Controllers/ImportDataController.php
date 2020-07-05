<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ImportData;
use App\Imports\AccountsImport;
use App\Imports\DataImport;
use App\Imports\JournalImport;
use App\Imports\LedgerImport;
use Str;
use Auth;
use Excel;
use DB;
class ImportDataController extends Controller
{
    public function import(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();
        $rules = [
            'file'=>'required',
        ];
        $attr = [
            'file'=>'File',
        ];
        
        $validator = \Validator::make($request->all(), $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $name = $request->name;
        $filename = \Str::slug('import_data_'.$name.'_'.date('Y m d H i s').' '.time(),'_');
        $filename = upload_file('file', $filename, 'public/files/import');
        if($name=='account'){
            \Excel::import(new AccountsImport($company->id, $user->id), storage_path("/app/".$filename));
            add_log('accounts', 'import', json_encode(['name'=>'Chart of Account File', 'url'=>url_file($filename)]));
            return redirect()->route('accounts.index')->with('success', 'Akun  baru berhasil ditambahkan');
        }else if($name=='department'){
            \Excel::import(new DataImport($company->id, $user->id, 'departments',['custom_id'=>'custom_id', 'name'=>'name'], ['description'=>'description']), storage_path("/app/".$filename));
            add_log('companies', 'import', json_encode(['name'=>'Departments File', 'url'=>url_file($filename)]));
            return redirect()->route('departments.index')->with('success', __('Data imported successfully.'));
        }else if($name=='tags'){
            \Excel::import(new DataImport($company->id, $user->id, 'tags',['group'=>'group', 'item_id'=>'code'], ['item_name'=>'name']), storage_path("/app/".$filename));
            add_log('companies', 'import', json_encode(['name'=>'Tags File', 'url'=>url_file($filename)]));
            return redirect()->route('tags.index')->with('success', __('Data imported successfully.'));
        }else if($name=='contact'){
            \Excel::import(new DataImport($company->id, $user->id, 'contacts',
            ['custom_id'=>'custom_id'], 
            ['name'=>'name', 'is_customer'=>'is_customer', 'is_supplier'=>'is_supplier', 'is_employee'=>'is_employee', 
            'is_others'=>'is_others', 'email'=>'email', 'phone'=>'phone', 'mobile'=>'mobile', 'address'=>'address']
            ), storage_path("/app/".$filename));
            add_log('companies', 'import', json_encode(['name'=>'Tags File', 'url'=>url_file($filename)]));
            return redirect()->route('contacts.index')->with('success', __('Data imported successfully.'));
        }else if($name=='journal'){
            $excel = \Excel::import(new \App\Imports\LedgerImport($company->id, $user->id), storage_path("/app/".$filename));
            add_log('journals', 'import', json_encode(['name'=>'Journal File', 'url'=>url_file($filename)]));
            return redirect()->route('dcru.index', 'journals')->with('success', 'Jurnla berhasil ditambahkan'); 
        }
    }
    
    public function execute($import){
        $company_id = $import->company_id;
        $user_id = $import->created_by;
        if($import->target=='account'){
            $excel = Excel::import(new AccountsImport($company_id, $user_id), storage_path('/app/'.$import->file));
        }else
        if($import->target=='ledger'){
            $excel = Excel::import(new LedgerImport($company_id, $user_id), storage_path('/app/'.$import->file));
        }
        return true;
    }

    public function transfer(Request $request){
        $target_company = company('id');
        if($request->type=='account'){
            $this->accountTransfer($request->company_id, $target_company);
            return redirect()->route('accounts.index')->with('success', __('Data transfered successfully to active company.'));
        }
        if($request->type=='department'){
            $this->departmentTransfer($request->company_id, $target_company);
            return redirect()->route('departments.index')->with('success', __('Data transfered successfully to active company.'));
        }
        if($request->type=='contact'){
            $this->contactTransfer($request->company_id, $target_company);
            return redirect()->route('contacts.index')->with('success', __('Data transfered successfully to active company.'));
        }
        if($request->type=='tags'){
            $this->tagsTransfer($request->company_id, $target_company);
            return redirect()->route('tags.index')->with('success', __('Data transfered successfully to active company.'));
        }
        return redirect()->route('companies.transfer')->with('error', __('Something error!'));
    }



    public function accountTransfer($source_company, $target_company){
        $query = DB::table('accounts')->where('company_id', $source_company);
        $user_id = user('id');
        $rowcount = $query->count();
        if($rowcount==0){
            return;
        }
        $query=$query->orderBy('tree_level', 'asc');
        $query->chunkById($rowcount, function ($accounts) use($target_company, $user_id) {
            foreach ($accounts as $account) {
                $parent = null;
                    if($account->account_parent_id!=null){
                        $parent = DB::table(DB::raw('accounts a'))
                        ->join(DB::raw('accounts b'), function($join)use($target_company){
                            $join->on('a.account_no', '=','b.account_no')->where('b.company_id', $target_company);
                        })
                        ->where('a.id', $account->account_parent_id)
                        ->value(DB::raw('b.id'));
                    }
                DB::table('accounts')
                    ->updateOrInsert(
                        [
                            'company_id'=>$target_company, 'account_no'=>$account->account_no, 'account_name'=>$account->account_name
                        ],
                        [
                            'sequence'=>$account->sequence,
                            'account_name_en'=>$account->account_name_en,
                            'account_parent_id'=>$parent,
                            'has_children'=>$account->has_children,
                            'tree_level'=>$account->tree_level,
                            'account_type_id'=>$account->account_type_id,
                            'account_mapping'=>$account->account_mapping,
                            'op_debit'=>$account->op_debit,
                            'op_credit'=>$account->op_credit,
                            'op_date'=>$account->op_date,
                            'type'=>$account->type,
                            'created_by'=>$user_id, 
                        ]
                    );
            }
        });
    }
    public function departmentTransfer($source_company, $target_company){
        $query = DB::table('departments')->where('company_id', $source_company);
        $user_id = user('id');
        $rowcount = $query->count();
        if($rowcount==0){
            return;
        }
        $query->chunkById($rowcount, function ($results) use($target_company, $user_id) {
            foreach ($results as $result) {
                DB::table('departments')
                    ->updateOrInsert(
                        [
                            'company_id'=>$target_company, 'custom_id'=>$result->custom_id, 'name'=>$result->name
                        ],
                        [
                            'description'=>$result->description,
                            'created_by'=>$user_id,
                        ]
                    );
            }
        });
    }
    public function contactTransfer($source_company, $target_company){
        $query = DB::table('contacts')->where('company_id', $source_company);
        $user_id = user('id');
        $rowcount = $query->count();
        if($rowcount==0){
            return;
        }
        $query->chunkById($rowcount, function ($results) use($target_company, $user_id) {
            foreach ($results as $result) {
                DB::table('contacts')
                    ->updateOrInsert(
                        [
                            'company_id'=>$target_company, 
                            'custom_id'=>$result->custom_id, 
                            'name'=>$result->name
                        ],
                        [
                            'is_customer'=>$result->is_customer,
                            'is_supplier'=>$result->is_supplier,
                            'is_employee'=>$result->is_employee,
                            'is_others'=>$result->is_others,
                            'email'=>$result->email,
                            'phone'=>$result->phone,
                            'mobile'=>$result->mobile,
                            'fax'=>$result->fax,
                            'tax_no'=>$result->tax_no,
                            'address'=>$result->address,
                            'shipping_address'=>$result->shipping_address,
                            'account_receivable'=>$result->account_receivable,
                            'account_payable'=>$result->account_payable,
                            'created_by'=>$user_id,
                        ]
                    );
            }
        });
    }
    public function tagsTransfer($source_company, $target_company){
        $query = DB::table('tags')->where('company_id', $source_company);
        $user_id = user('id');
        $rowcount = $query->count();
        if($rowcount==0){
            return;
        }
        $query->chunkById($rowcount, function ($results) use($target_company, $user_id) {
            foreach ($results as $result) {
                DB::table('tags')
                    ->updateOrInsert(
                        [
                            'company_id'=>$target_company, 
                            'group'=>$result->group, 
                            'item_id'=>$result->item_id
                        ],
                        [
                            'item_name'=>$result->item_name,
                            'created_by'=>$user_id,
                        ]
                    );
            }
        });
    }
    public function journalTransfer($source_company, $target_company){
        $query = DB::table('journals')->where('company_id', $source_company);
        $user_id = user('id');
        $rowcount = $query->count();
        if($rowcount==0){
            return;
        }
        $query->chunkById($rowcount, function ($results) use($target_company, $user_id) {
            foreach ($results as $result) {
                DB::table('tags')
                    ->updateOrInsert(
                        [
                            'company_id'=>$target_company, 
                            'group'=>$result->group, 
                            'item_id'=>$result->item_id
                        ],
                        [
                            'item_name'=>$result->item_name,
                            'created_by'=>$user_id,
                        ]
                    );
            }
        });
    }

    
}
