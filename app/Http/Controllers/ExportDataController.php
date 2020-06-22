<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\JournalExport;
use App\Exports\AccountExport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
class ExportDataController extends Controller
{
    public function excel(Request $request, $name){
        $company = Auth::user()->activeCompany();
        if($name=='account'){
            return Excel::download(new AccountExport($company->id), 'account.xlsx');
        }else
        if($name=='ledger'){
            return Excel::download(new JournalExport($company->id), 'ledger.xlsx');
        }
    }
}
