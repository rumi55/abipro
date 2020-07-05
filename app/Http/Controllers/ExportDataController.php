<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\JournalExport;
use App\Exports\AccountExport;
use App\Exports\DataExport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use DB;
class ExportDataController extends Controller
{
    public function excel(Request $request, $name){
        $company = Auth::user()->activeCompany();
        $data = $this->$name($request->all());
        return Excel::download(new DataExport($data['headings'], $data['results']), $name.'.xlsx');
    }

    //data reference

    private function account($params){
        $company = Auth::user()->activeCompany();
        $headings = [
            'account_no',
            'account_name',
            'account_type',
            'account_parent_no',
        ];
        $results = DB::table(DB::raw('accounts a'))
        ->leftJoin(DB::raw('accounts b'), DB::raw('b.id'), '=', DB::raw('a.account_parent_id'))
        ->where('a.company_id', $company->id)
        ->selectRaw("a.account_no, a.account_name, a.account_type_id as account_type, b.account_no as account_parent_no")
        ->orderBy('a.account_type_id', 'asc')
        ->orderBy('a.sequence', 'asc')
        ->get();
        return ['headings'=>$headings, 'results'=>$results];
    }
    private function department($params){
        $company = Auth::user()->activeCompany();
        $headings = [
            'custom_id',
            'name',
            'description',
        ];
        $results = DB::table('departments')
        ->where('company_id', $company->id)
        ->select(['custom_id', 'name', 'description'])
        ->orderBy('custom_id', 'asc')
        ->get();
        return ['headings'=>$headings, 'results'=>$results];
    }
    private function contact($params){
        $company = Auth::user()->activeCompany();
        $headings = [
            'custom_id',
            'name',
            'is_customer',
            'is_supplier',
            'is_employee',
            'is_others',
            'email',
            'phone',
            'mobile',
            'address',
        ];
        $results = DB::table('contacts')
        ->where('company_id', $company->id)
        ->select(['custom_id', 'name', 'is_customer', 'is_supplier', 'is_employee', 'is_others', 'email', 'phone', 'mobile', 'address'])
        ->orderBy('custom_id', 'asc')
        ->get();
        return ['headings'=>$headings, 'results'=>$results];
    }
    private function tags($params){
        $company = Auth::user()->activeCompany();
        $headings = [
            'code',
            'name',
            'group',
        ];
        $results = DB::table('tags')
        ->where('company_id', $company->id)
        ->selectRaw('item_id as code, item_name as name, `group`')
        ->orderBy('group', 'asc')
        ->get();
        return ['headings'=>$headings, 'results'=>$results];
    }

    //data transaction

    private function voucher($params){
        $company = Auth::user()->activeCompany();
        $headings = [
            'transaction_date',
            'transaction_no',
            'sequence',
            'account_no',
            'description',
            'debit',
            'credit',
            'total',
        ];
        $results = DB::table(DB::raw('vw_voucher'))->where('company_id', $company->id)
        ->select(['trans_date', 'trans_no', 'sequence', 'account_no','description', 'debit', 'credit', 'total'])
        ->orderBy('trans_date', 'asc')->orderBy('trans_no', 'asc')->get();
        return ['headings'=>$headings, 'results'=>$results];
    }
    private function journal($params){
        $company = Auth::user()->activeCompany();
        $headings = [
            'transaction_date',
            'transaction_no',
            'description',
            'sequence',
            'account_no',
            'department',
            'detail_description',
            'debit',
            'credit',
            'total',
        ];
        $results = DB::table(DB::raw('journals a'))
        ->leftJoin(DB::raw('journal_details b'), DB::raw('a.id'), '=', DB::raw('b.journal_id'))
        ->leftJoin(DB::raw('accounts c'), DB::raw('b.account_id'), '=', DB::raw('c.id'))
        ->leftJoin(DB::raw('departments d'), DB::raw('b.department_id'), '=', DB::raw('d.id'))
        ->where('a.company_id', $company->id)
        ->selectRaw("trans_date, trans_no, a.description as description, b.sequence, account_no, d.custom_id AS department,b.description AS detail_description, debit, credit, total")
        ->orderBy('trans_date', 'asc')->orderBy('trans_no', 'asc')->get();
        return ['headings'=>$headings, 'results'=>$results];
    }
}
