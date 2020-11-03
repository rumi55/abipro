<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Department;
use App\Http\Resources\CashflowReportResource;
use App\Http\Resources\DepartmentResource;
use DB;

class CashflowReportController extends Controller
{
    public function index(Request $request){
        $company = \Auth::user()->activeCompany();
        $company_id = $company->id;
        $params = $this->getParams($request, $company_id);
        $params['detail'] = false;
        if($params['detail']){
            $results = $this->query($params, $company);
        }else{
            $results = $this->query2($params, $company);
        }

        $title = 'Laporan Arus Kas';
        $view = 'report.cashflow.detail';
        // $view = 'report.cashflow.default';
        $period = ($params['start_date']==$params['end_date'])?fdate($params['start_date'], 'd M Y'):fdate($params['start_date'], 'd M Y').' s.d '.fdate($params['end_date'], 'd M Y');
        $balance_date = \Carbon\Carbon::parse($params['start_date'])->subDay()->format('Y-m-d');
        $departments = Department::whereIn('id', $params['department_id'])->get();

        $data = array(
            'report'=>'cashflow',
            'title'=>$title,
            'company'=>$company,
            'period'=>$period,
            'departments'=>$departments,
            'income'=>$results[0],
            'expense'=>$results[1],
            'balance'=>$results[2],
            'start_balance_date'=>$balance_date,
            'end_balance_date'=>$params['end_date'],
            'view'=>$view
        );
        $data = array_merge($data, $params);
        if(isset($request->output)){
            $output = $request->output;
            if($output=='excel'){
                header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
                header("Content-Disposition: attachment; filename=journals.xls");
                return $this->html($view, $data);
            }
        }
        return $this->pdf($view, $data);
    }

    private function html($view, $data){
        return view('report.viewer', $data);
    }
    private function pdf($view, $data){
        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('report.pdf', $data);
        return $pdf->stream($data['report'].'.pdf');
    }

    private function query($params, $company){
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $departments = '';

        $balance = DB::table('vw_journals')
        ->selectRaw("IF(SUM(vw_journals.total) IS NULL, 0,SUM(vw_journals.total)) as balance")
        ->where('company_id', $company->id)
        ->where('account_type_id', '=',1)
        ->whereDate('trans_date', '<', $start_date);
        if(count($params['department_id'])>0){
            $balance = $balance->whereIn('department_id', $params['department_id']);
        }
        $balance = $balance->value('balance');
        $opening_balance = DB::table('vw_accounts')
        ->selectRaw("SUM(balance) as balance")
        ->where('company_id', $company->id)
        ->where('account_type_id', '=',1)
        ->value('balance');
        if(count($params['department_id'])==0){
            $balance += $opening_balance;
        }

        $income = DB::table('vw_journals')
        ->where('company_id', $company->id)
        ->where('account_type_id', '<>',1)
        ->whereDate('trans_date', '>=', $start_date)
        ->whereDate('trans_date', '<=', $end_date)
        ->where('debit', '=', 0)
        ->whereRaw(
            "journal_id IN (SELECT journal_id FROM vw_journals WHERE account_type_id=1 AND company_id=$company->id)"
        );
        if(count($params['department_id'])>0){
            $income = $income->whereIn('department_id', $params['department_id']);
        }
        $income = $income
        ->orderBy('trans_date')->orderBy('trans_no')->get();

        $expense = DB::table('vw_journals')
        ->where('company_id', $company->id)
        ->where('account_type_id', '<>',1)
        ->whereDate('trans_date', '>=', $start_date)
        ->whereDate('trans_date', '<=', $end_date)
        ->where('credit', '=', 0)
        ->whereRaw(
            "journal_id IN (SELECT journal_id FROM vw_journals WHERE account_type_id=1 AND company_id=$company->id)"
        );
        if(count($params['department_id'])>0){
            $expense = $expense->whereIn('department_id', $params['department_id']);
        }
        $expense = $expense
        ->orderBy('trans_date')->orderBy('trans_no')->get();
        return [$income, $expense, $balance];
    }
    private function query2($params, $company){
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $departments = '';

        $balance = DB::table('vw_journals')
        ->selectRaw("IF(SUM(vw_journals.total) IS NULL, 0,SUM(vw_journals.total)) as balance")
        ->where('company_id', $company->id)
        ->where('account_type_id', '=',1)
        ->whereDate('trans_date', '<', $start_date);
        if(count($params['department_id'])>0){
            $balance = $balance->whereIn('department_id', $params['department_id']);
        }
        $balance = $balance->value('balance');
        $opening_balance = DB::table('vw_accounts')
        ->selectRaw("SUM(balance) as balance")
        ->where('company_id', $company->id)
        ->where('account_type_id', '=',1)
        ->value('balance');
        if(count($params['department_id'])==0){
            $balance += $opening_balance;
        }

        $income = DB::table('vw_journals')
        ->selectRaw("account_id, account_no, account_name, IF(SUM(vw_journals.total) IS NULL, 0,SUM(vw_journals.total)) as total")
        ->where('company_id', $company->id)
        ->where('account_type_id', '<>',1)
        ->whereDate('trans_date', '>=', $start_date)
        ->whereDate('trans_date', '<=', $end_date)
        ->where('debit', '=', 0)
        ->whereRaw(
            "journal_id IN (SELECT journal_id FROM vw_journals WHERE account_type_id=1 AND company_id=$company->id)"
        );
        if(count($params['department_id'])>0){
            $income = $income->whereIn('department_id', $params['department_id']);
        }
        $income = $income
        ->groupBy(DB::raw('account_id, account_no, account_name'))
        ->orderBy('account_no')->get();

        $expense = DB::table('vw_journals')
        ->selectRaw("account_id, account_no, account_name, IF(SUM(vw_journals.total) IS NULL, 0,SUM(vw_journals.total)) as total")
        ->where('company_id', $company->id)
        ->where('account_type_id', '<>',1)
        ->whereDate('trans_date', '>=', $start_date)
        ->whereDate('trans_date', '<=', $end_date)
        ->where('credit', '=', 0)
        ->whereRaw(
            "journal_id IN (SELECT journal_id FROM vw_journals WHERE account_type_id=1 AND company_id=$company->id)"
        );
        if(count($params['department_id'])>0){
            $expense = $expense->whereIn('department_id', $params['department_id']);
        }
        $expense = $expense
        ->groupBy(DB::raw('account_id, account_no, account_name'))
        ->orderBy('account_no')->get();

        return [$income, $expense, $balance];
    }


    private function getParams(Request $request, $company_id){
        $departments = $request->query('departments',[]);
        $accounts = $request->query('accounts',[]);
        $start_date = $request->query('start_date', date('Y-m-d'));
        $end_date = $request->query('end_date', date('Y-m-d'));

        $detail = filter_var($request->detail, FILTER_VALIDATE_BOOLEAN);

        $params = [
            'department_id'=>$departments,
            'account_id'=>$accounts,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'detail'=>$detail
        ];
        return $params;
    }

}
