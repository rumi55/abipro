<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Account;
use Auth;
use Str;

class ConvertAbiproController extends Controller
{
    public function index()
    {
        return view('convert.index');
    }
    public function accountTypeMapping(Request $request)
    {
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $account = Account::where('company_id', $company_id);
        $account = $account->orderBy('sequence', 'asc');
        $accounts = $account->get();
        return view('convert.account_type_mapping', compact('accounts'));
    }
    public function accountTypeMappingSave(Request $request)
    {
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $account_types = $request->account_type_id;
        foreach ($account_types as $id => $account_type) {
            $account = Account::findOrFail($id);
            $account->account_type_id = $account_type;
            $seq = Account::where('company_id', $company_id)
                ->whereNull('account_parent_id')
                ->where('account_type_id', $account_type)
                ->orderBy('sequence', 'desc')->first();
            if ($seq == null) {
                $sequence = $account_type * 1000;
            } else {
                $sequence = $seq->sequence + 1;
            }
            $account->sequence = $sequence;
            $account->save();
        }
        return redirect()->route('convert.accounts', ['step' => 3])->with('success', 'Account type successfully saved.');
    }
    public function departmentConversion(Request $request)
    {
        $data = array();
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        return view('convert.department');
    }
    public function sortirConversion(Request $request)
    {
        $data = array();
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        return view('convert.sortir');
    }
    public function journalConversion(Request $request)
    {
        $data = array();
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        return view('convert.journal');
    }
    public function accountConversion(Request $request)
    {
        $data = array();
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        if ($request->step == 2) {
            $account = Account::where('company_id', $company_id);
            $account = $account->orderBy('sequence', 'asc');
            $accounts = $account->get();
            $data['accounts'] = $accounts;
        }
        return view('convert.account', $data);
    }
    public function execute(Request $request, $id)
    {
        $user = Auth::user();
        $company = $user->activeCompany();
        $import = \App\ImportData::where('target', $id)->where('company_id', $company->id)->first();
        if ($import == null) {
            return response()->json(['status' => 'error', 'message' => 'File not found']);
        }
        $name = $import->target;
        $dbf_path = $import->file;
        $columns = $this->columnMappings();
        if ($name == 'gltran') {
            return $this->gltran($dbf_path);
        }
        if (in_array($name, ['gls1', 'gls2', 'gls3', 'gls4', 'gls5', 'gls6'])) {
            // return response()->json(['status'=>'success',     'message'=>$column['gls']]);
            $data = $this->readdbf($dbf_path, $columns['gls']);
            return $this->gls($company->id, $data, $name);
        }
        $data = $this->readdbf($dbf_path, $columns[$name]);
        return $this->$name($company->id, $data);
    }
    public function upload(Request $request, $name)
    {
        $user = Auth::user();
        $company = $user->activeCompany();
        $filename = Str::slug($company->name . ' ' . $name, '-');
        $filename = upload_file('gldept', $filename, 'public/dbf');
        $dbf_path = storage_path("/app/$filename");
        $columns = $this->columnMappings();
        if ($name == 'gltran') {
            return $this->gltran($dbf_path);
        }
        if (in_array($name, ['gls1', 'gls2', 'gls3', 'gls4', 'gls5', 'gls6'])) {
            return response()->json(['status' => 'success', 'message' => $column['gls']]);
            // $data = $this->readdbf($dbf_path, $columns['gls']);
            // return $this->$name($company->id, $data, $name);
        }
        // if($name=='gls'){
        // }
        $data = $this->readdbf($dbf_path, $columns[$name]);
        return $this->$name($company->id, $data);
    }
    public function gltype($company_id, $data)
    {
        $newdata = array();
        foreach ($data as $dt) {
            if(empty($dt['account_no']) || empty($dt['account_name']) || empty($dt['account_type_id'])){
                continue;
            }
            $account_type = intval($dt['account_type_id']);
            $dt['company_id'] = $company_id;
            $dt['tree_level'] = 0;
            $dt['has_children'] = 0;
            $dt['sequence'] = $account_type . '-' . $dt['account_no'];
            $newdata[] = $dt;
        }
        \DB::table('accounts')->where('company_id', $company_id)->delete();
        \DB::table('accounts')->insert($newdata);
        return response()->json(['status' => 'success', 'message' => 'gltype converted successfully']);
        //return redirect()->route('convert.accounts', ['step'=>2])->with('success', 'Gltype conversion successfully.');
    }
    public function glnama($company_id, $data)
    {
        $newdata = array();
        try{
            \DB::beginTransaction();
            foreach ($data as $dt) {
                //ada hardcode
                if(empty($dt['account_no']) || empty($dt['account_name']) || empty($dt['account_parent_id']) || $dt['account_name']=='AAS'){
                    continue;
                }
                $dt['company_id'] = $company_id;
                $parent_no = $dt['account_parent_id'];
                $parent = Account::where('company_id', $company_id)->where('account_no', $parent_no)->first();
                if ($parent != null) { //jika parent ada
                    $parent->has_children = true;
                    $parent->save();
                    $parent_id = $parent->id;
                    $tree_level = $parent->tree_level + 1;
                    $account_type = $parent->account_type_id; //tipe akun sama dengan parent
                    $dt['account_parent_id'] = $parent_id;
                    $dt['sequence'] = $parent->sequence . '-' . $dt['account_no'];
                    $dt['account_type_id'] = $account_type;
                    $dt['tree_level'] = $tree_level;
                    $dt['has_children'] = 0;
                }
                $newdata[] = $dt;
            }
            \DB::table('accounts')->insert($newdata);
            \DB::commit();
        }catch(Exception $e){
            \DB::rollback();
        }
        return response()->json(['status' => 'success', 'message' => 'glnama converted successfully']);
    }
    public function glmast($company_id, $data)
    {
        $balance = array();
        $budgets = array();
        foreach ($data as $dt) {
            $dt['company_id'] = $company_id;
            $account_no = $dt['account_no'];
            $account = Account::where('company_id', $company_id)->where('account_no', $account_no)->first();
            $department_id = null;
            if (!empty($dt['department'])) {
                $department = \App\Department::where('company_id', $company_id)->where('custom_id', $dt['department'])->first();
                $department_id = $department != null ? $department->id : null;
            }
            if ($account != null) {
                $balance[] = [
                    'company_id' => $company_id,
                    'department_id' => $department_id,
                    'account_id' => $account->id,
                    'balance' => $dt['opening_balance'],
                    'created_by' => user('id'),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $budgets[] = [
                    'company_id' => $company_id,
                    'department_id' => $department_id,
                    'account_id' => $account->id,
                    'jan' => $dt['jan'],
                    'feb' => $dt['feb'],
                    'mar' => $dt['mar'],
                    'apr' => $dt['apr'],
                    'may' => $dt['may'],
                    'jun' => $dt['jun'],
                    'jul' => $dt['jul'],
                    'aug' => $dt['aug'],
                    'sep' => $dt['sep'],
                    'oct' => $dt['oct'],
                    'nov' => $dt['nov'],
                    'dec' => $dt['dec'],
                    'created_by' => user('id'),
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
        }
        \DB::table('balances')->where('company_id', $company_id)->delete();
        \DB::table('balances')->insert($balance);
        \DB::table('budgets')->where('company_id', $company_id)->delete();
        \DB::table('budgets')->insert($budgets);
        return response()->json(['status' => 'success', 'message' => 'glmast converted successfully']);
    }

    public function gldept($company_id, $data)
    {
        $newdata = array();
        foreach ($data as $dt) {
            $dt['company_id'] = $company_id;
            $dt['created_at'] = date('Y-m-d H:i:s');
            $dt['created_by'] = user('id');
            $newdata[] = $dt;
        }
        \DB::table('departments')->where('company_id', $company_id)->delete();
        \DB::table('departments')->insert($newdata);
        return response()->json(['status' => 'success', 'message' => 'gldept converted successfully']);
        // return redirect()->route('departments.index')->with('success', 'Gldept conversion successfully.');
    }
    public function gls($company_id, $data, $sortir)
    {
        $newdata = array();
        foreach ($data as $dt) {
            $dt['group'] = $sortir;
            $dt['company_id'] = $company_id;
            $dt['created_at'] = date('Y-m-d H:i:s');
            $dt['created_by'] = user('id');
            $newdata[] = $dt;
        }
        \DB::table('tags')->insert($newdata);
        return response()->json(['status' => 'success', 'message' => $sortir . ' converted successfully']);
        // return redirect()->route('tags.index')->with('success', 'Glsortir conversion successfully.');
    }

    // public function gltrans($company_id, $data){
    //     // $newdata = array();
    //     // foreach($data as $dt){
    //     //     $dt['company_id']=$company_id;
    //     //     $dt['tree_level']=0;
    //     //     $dt['has_children']=0;

    //     //     $newdata[]=$dt;
    //     // }
    //     // \DB::table('accounts')->where('company_id', $company_id)->delete();
    //     // \DB::table('accounts')->insert($newdata);
    //     return redirect()->route('convert.accounts', ['step'=>2])->with('success', 'Gltype conversion successfully.');
    // }

    private function readdbf($dbf_file, $columns)
    {
        $dbf = dbase_open($dbf_file, 0);
        $column_info = dbase_get_header_info($dbf);
        $num_rec = dbase_numrecords($dbf);
        $num_fields = dbase_numfields($dbf);
        $data = array();
        for ($i = 1; $i <= $num_rec; $i++) {
            $dbf_row = dbase_get_record_with_names($dbf, $i);
            $row = array();
            $empty = '';
            foreach ($dbf_row as $key => $val) {
                if ($key == 'deleted') {
                    continue;
                }
                if (array_key_exists($key, $columns)) {
                    $row[$columns[$key]] = trim($val);
                    $empty .= trim($val);
                }
            }
            if (!(empty($empty))) {
                $data[] = $row;
            }
        }
        return $data;
    }
    private function gltran($dbf_file)
    {
        $dbf = dbase_open($dbf_file, 0);
        $column_info = dbase_get_header_info($dbf);
        $num_rec = dbase_numrecords($dbf);
        $num_fields = dbase_numfields($dbf);
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $user_id = $user->id;
        try {
            \DB::beginTransaction();
            //delete all existing journal
            \App\Journal::where('company_id', $company_id)->delete();
            $prev_no = null;
            for ($i = 1; $i <= $num_rec; $i++) {
                $row = dbase_get_record_with_names($dbf, $i);
                $trans_date = fdate($row['GLT_TGL'], 'Y-m-d');
                $trans_no = trim($row['GLT_BUKTI']);
                $description = trim($row['GLT_KET']);
                $account_no = trim($row['GLT_ACCOUN']);
                $created_at = date('Y-m-d H:i:s');
                $sequence = trim($row['GLT_URUT']);
                $debit = abs($row['DEBET']);
                $credit = abs($row['KREDIT']);
                $total = abs($row['GLT_JUMLAH']);
                $department_ci = $row['GL_DEPT'];
                $tags_1 = $row['S1'];
                $tags_2 = $row['S2'];
                $tags_3 = $row['S3'];
                $tags_4 = $row['S4'];
                $tags_5 = $row['S5'];
                $tags_6 = $row['S6'];
                //account
                //asumsinya akunnya sudah ada di database
                $account = Account::where('company_id', $company_id)->where('account_no', $account_no)->first();
                if ($account == null) {
                    continue;
                }
                //depart
                $department_id = null;
                $tag_id = null;
                //cari id department by kode departement
                if (!empty($department_ci)) {
                    $department = \App\Department::where('company_id', $company_id)->where('custom_id', $department_ci)->first();
                    $department_id = $department != null ? $department->id : null;
                }
                $tags = [];
                if (!empty($tags_1)) {
                    $tag = \App\Tag::where('company_id', $company_id)->where('item_id', $tags_1)->first();
                    if ($tag != null) {
                        $tags[] = $tag->id;
                    }
                }
                if (!empty($tags_2)) {
                    $tag = \App\Tag::where('company_id', $company_id)->where('item_id', $tags_2)->first();
                    if ($tag != null) {
                        $tags[] = $tag->id;
                    }
                }
                if (!empty($tags_3)) {
                    $tag = \App\Tag::where('company_id', $company_id)->where('item_id', $tags_3)->first();
                    if ($tag != null) {
                        $tags[] = $tag->id;
                    }
                }
                if (!empty($tags_4)) {
                    $tag = \App\Tag::where('company_id', $company_id)->where('item_id', $tags_4)->first();
                    if ($tag != null) {
                        $tags[] = $tag->id;
                    }
                }
                if (!empty($tags_5)) {
                    $tag = \App\Tag::where('company_id', $company_id)->where('item_id', $tags_5)->first();
                    if ($tag != null) {
                        $tags[] = $tag->id;
                    }
                }
                if (!empty($tags_6)) {
                    $tag = \App\Tag::where('company_id', $company_id)->where('item_id', $tags_6)->first();
                    if ($tag != null) {
                        $tags[] = $tag->id;
                    }
                }

                $tag_id = count($tags) > 0 ? implode(',', $tags) : '';
                //check journal
                $journal = \App\Journal::where('company_id', $company_id)->where('trans_no', $trans_no)->first();

                if ($journal == null) {
                    $numbering = \App\Numbering::where('company_id', $company_id)->where('transaction_type_id', \App\TransactionType::JOURNAL)->first();
                    if ($numbering->counter_reset == 'y') {
                        $period = date('Y');
                    } else if ($numbering->counter_reset == 'm') {
                        $period = date('Y-m');
                    } else if ($numbering->counter_reset == 'd') {
                        $period = date('Y-m-d');
                    } else {
                        $period  = null;
                    }
                    $counter = \App\Counter::firstOrCreate(
                        ['period' => $period, 'numbering_id' => $numbering->id, 'company_id' => $company_id],
                        ['counter' => $numbering->counter_start - 1]
                    );

                    $check = true;
                    do {
                        $counter->getNumber();
                        $jtrans_no = $counter->last_number;
                        $c = \App\Journal::where('trans_no', $trans_no)->where('company_id', $company_id)->count();

                        if ($c == 0) {
                            $journal = \App\Journal::create([
                                'journal_id' => $jtrans_no,
                                'trans_no' => $trans_no,
                                'trans_date' => $trans_date,
                                'description' => $description,
                                'company_id' => $company_id,
                                'is_voucher' => 0,
                                'total' => $total,
                                'transaction_type_id' => \App\TransactionType::JOURNAL,
                                'created_by' => $user_id,
                                'created_at' => $created_at
                            ]);
                            $counter->save();
                            $check = false;
                        }
                    } while ($check);
                }
                // if ($prev_no != $trans_no) {
                //     $sequence = 0;
                //     $prev_no = $trans_no;
                // } else {
                //     $sequence++;
                // }

                \App\JournalDetail::create([
                    'trans_date' => $trans_date,
                    'sequence' => $sequence,
                    'account_id' => $account->id,
                    'description' => $description,
                    'department_id' => $department_id,
                    'tags' => $tag_id,
                    'debit' => $debit,
                    'credit' => $credit,
                    'journal_id' => $journal->id,
                    'created_by' => $user_id,
                    'created_at' => $created_at
                ]);
            }
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollback();
        }
        return response()->json(['status' => 'success', 'message' => 'gltrans converted successfully']);
        // return redirect()->route('journals.index')->with('success', 'Gltran coverted successfully');
    }
    private function columnMappings()
    {
        return array(
            'gltype' => [
                'F_KODE' => 'account_no', 'F_NAMA' => 'account_name', 'F_GOL' => 'account_type_id'
            ],
            'glnama' => [
                'KODE' => 'account_no', 'NAMA' => 'account_name', 'TYPE' => 'account_parent_id'
            ],
            'glmast' => [
                'GL_KODE' => 'account_no', 'GL_NAMA' => 'account_name', 'GL_TYPE' => 'account_type_id', 'GL_DEPT' => 'department',
                'GL_AWAL' => 'opening_balance',
                'BUD_01'=>'jan',
                'BUD_02'=>'feb',
                'BUD_03'=>'mar',
                'BUD_04'=>'apr',
                'BUD_05'=>'may',
                'BUD_06'=>'jun',
                'BUD_07'=>'jul',
                'BUD_08'=>'aug',
                'BUD_09'=>'sep',
                'BUD_10'=>'oct',
                'BUD_11'=>'nov',
                'BUD_12'=>'dec',
            ],
            'gldept' => [
                'KODE' => 'custom_id', 'NAMA' => 'name'
            ],
            'gls' => [
                'KODE' => 'item_id', 'NAMA' => 'item_name'
            ]

        );
    }
}
