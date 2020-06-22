<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ImportData;
use App\Imports\AccountsImport;
use App\Imports\JournalImport;
use App\Imports\LedgerImport;
use Str;
use Auth;
use Excel;

class ImportDataController extends Controller
{
    public function upload(Request $request){
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $import = new ImportData;
        $filename = Str::slug('import_data_'.$request->target.'_'.date('Y m d H i s').' '.time(),'_');
        $filename = upload_file('file', $filename, 'public/files/import');
        $import->company_id = $company_id;
        $import->file = $filename;
        $import->target = $request->target;
        $import->status = 'uploaded';
        $import->created_by = $user->id;
        $import->save();
        
        if($this->execute($import)){
            $import->status = 'executed';
        }else{
            $import->status = 'failed';
        }
        $import->save();
        return $import;
    }

    public function import(Request $request, $id){
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $import = ImportData::find($id);
        // echo storage_path("/app/".$import->file);return;
        if($import->target=='account'){
            $excel = Excel::import(new AccountsImport($company_id), storage_path('/app/'.$import->file));
        }else
        if($import->target=='journal'){
            $excel = Excel::import(new JournalImport($company_id, $user->id), storage_path('/app/'.$import->file));
        }else
        if($import->target=='ledger'){
            $excel = Excel::import(new LedgerImport($company_id, $user->id), storage_path('/app/'.$import->file));
        }
        return 'success';
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
}
