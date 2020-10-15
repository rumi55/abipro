<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Http\Resources\TrialBalanceReportResource;
use DB;

class TrialBalanceReportController extends Controller
{
    public function index(Request $request){
        $company = \Auth::user()->activeCompany();
        $company_id = $company->id;
        $params = $this->getParams($request, $company_id);
        if($request->version==2){
            $data = $this->query2($params, $company);
            $view = 'report.balance.trial_balance_v2';
        }else{
            $view = 'report.balance.trial_balance';
            $data = $this->query($params, $company);
        }

        // dd($data);
        $title = 'Laporan Neraca Saldo';
        $period = ($params['start_date']==$params['end_date'])?fdate($params['start_date'], 'd M Y'):fdate($params['start_date'], 'd M Y').' s.d '.fdate($params['end_date'], 'd M Y');
        $balance_date = \Carbon\Carbon::parse($params['start_date'])->subDay()->format('d-m-Y');

        $data = array(
            'report'=>'trial_balance',
            'title'=>$title,
            'period'=>$period,
            'opening_balance_date'=>$balance_date,
            'final_balance_date'=>fdate($params['end_date']),
            'company'=>$company,
            'end_date'=>$params['end_date'],
            'accounts'=>$data,
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
        $pdf->setPaper('a4', 'landscape');
        $pdf->loadView('report.pdf', $data);
        return $pdf->download($data['report'].'.pdf');
    }
    /**
     * Saldo hanya satu column
     *
     * */
    private function query($params, $company){
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $period = $company->getPeriod(fdate($start_date, 'Y'), fdate($start_date, 'm'));
        $last_period = $period[0];
        $start_period = $period[1];
        $end_period = $period[2];
        // DB::enableQueryLog();

        $balance = DB::table(DB::raw('vw_accounts a'))
        ->select([
            DB::raw('a.id, a.account_no, a.account_name'),
            DB::raw("(SELECT IF(SUM(d.debit) IS NULL,0,SUM(d.debit)) FROM vw_ledger d
            WHERE d.account_id=a.id AND a.company_id=d.company_id
            AND d.trans_date>='$start_date' AND d.trans_date<='$end_date'
            ) as debit"),
            DB::raw("(SELECT IF(SUM(e.credit) IS NULL, 0, SUM(e.credit)) FROM vw_ledger e
            WHERE e.account_id=a.id AND a.company_id=e.company_id
            AND e.trans_date>='$start_date' AND e.trans_date<='$end_date'
            ) as credit"),
            DB::raw("(SELECT IF(SUM(f.total) IS NULL, 0, SUM(f.total))+a.balance FROM vw_ledger f
            WHERE f.account_id=a.id AND a.company_id=f.company_id
            AND f.trans_date<'$start_date'
            ) as op_balance"),
            DB::raw("(SELECT IF(SUM(g.total) IS NULL, 0, SUM(g.total))+a.balance FROM vw_ledger g
            WHERE g.account_id=a.id AND a.company_id=g.company_id
            AND g.trans_date<='$end_date'
            ) as total_balance")
        ])
        ->whereRaw("a.company_id='$company->id' AND a.has_children=false");
        if(count($params['account_id'])>0){
            $balance = $balance->whereIn(DB::raw('a.id'), $params['account_id']);
        }

        // if(count($params['department_id'])>0){
        //     $balance = $balance->whereIn(DB::raw('a.department_id'), $params['department_id']);
        // }
        // $balance = $balance->groupBy(DB::raw('a.account_no, a.account_name, c.balance'));
        $balance = $balance->orderBy(DB::raw('a.account_no'))->get();

        // dd(DB::getQueryLog());
        return $balance;
    }
    /**
     * Menggunakan debet kredit untuk saldo awal dan akhir. Untuk backup saja.
     */
    private function query2($params, $company){
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $period = $company->getPeriod(fdate($start_date, 'Y'), fdate($start_date, 'm'));
        $last_period = $period[0];
        $start_period = $period[1];
        $end_period = $period[2];
        // DB::enableQueryLog();

        $cbooks = \App\ClosingBook::where('company_id', $company->id)->orderBy('end_date','desc')->get();
        $cutoff = $this->getCutoffDate($end_date, $cbooks);
        // dd($cbooks);
        $re1 = "(SELECT IF(SUM(cb.debit) IS NULL, 0, SUM(cb.debit)) FROM closing_books cb WHERE a.id=cb.account_id AND cb.end_date<'$start_date')";
        $re2 = "(SELECT IF(SUM(cb.debit) IS NULL, 0, SUM(cb.debit)) FROM closing_books cb WHERE a.id=cb.account_id AND cb.end_date<='$end_date')";
        $re3 = "(SELECT IF(SUM(cb.credit) IS NULL, 0, SUM(cb.credit)) FROM closing_books cb WHERE a.id=cb.account_id AND cb.end_date<'$start_date')";
        $re4 = "(SELECT IF(SUM(cb.credit) IS NULL, 0, SUM(cb.credit)) FROM closing_books cb WHERE a.id=cb.account_id AND cb.end_date<='$end_date')";

        $balance = DB::table(DB::raw('vw_accounts a'))
        ->select([
            DB::raw('a.id, a.account_no, a.account_name, a.account_group'),
            DB::raw("(SELECT IF(SUM(d.debit) IS NULL, 0, SUM(d.debit)) FROM vw_ledger d
            WHERE d.account_id=a.id AND a.company_id=d.company_id
            AND d.trans_date>='$start_date' AND d.trans_date<='$end_date'
            ) as debit"),
            DB::raw("(SELECT IF(SUM(e.credit) IS NULL,0, SUM(e.credit)) FROM vw_ledger e
            WHERE e.account_id=a.id AND a.company_id=e.company_id
            AND e.trans_date>='$start_date' AND e.trans_date<='$end_date'
            ) as credit"),
            DB::raw("(SELECT IF(SUM(f.debit) IS NULL,0, SUM(f.debit))+a.op_debit+$re1 FROM vw_ledger f
            WHERE f.account_id=a.id AND a.company_id=f.company_id
            AND f.trans_date<'$start_date'
            ) as op_debit"),
            DB::raw("(SELECT IF(SUM(g.debit) IS NULL,0, SUM(g.debit))+a.op_debit+$re2 FROM vw_ledger g
            WHERE g.account_id=a.id AND a.company_id=g.company_id
            AND g.trans_date<='$end_date'
            ) as total_debit"),
            DB::raw("(SELECT IF(SUM(h.credit) IS NULL,0, SUM(h.credit))+a.op_credit+$re3 FROM vw_ledger h
            WHERE h.account_id=a.id AND a.company_id=h.company_id
            AND h.trans_date<'$start_date'
            ) as op_credit"),
            DB::raw("(SELECT IF(SUM(i.credit) IS NULL,0, SUM(i.credit))+a.op_credit+$re4 FROM vw_ledger i
            WHERE i.account_id=a.id AND a.company_id=i.company_id
            AND i.trans_date<='$end_date'
            ) as total_credit"),
        ])
        ->whereRaw("a.company_id='$company->id' AND a.has_children=false AND account_group IN ('asset', 'liability', 'equity')");
        if(count($params['account_id'])>0){
            $balance = $balance->whereIn(DB::raw('a.id'), $params['account_id']);
        }
        $balance = $balance->orderBy(DB::raw('a.account_group'))->get();
        $income = DB::table(DB::raw('vw_accounts a'))
        ->select([
            DB::raw('a.id, a.account_no, a.account_name, a.account_group'),
            DB::raw("(SELECT IF(SUM(d.debit) IS NULL, 0, SUM(d.debit)) FROM vw_ledger d
            WHERE d.account_id=a.id AND a.company_id=d.company_id
            AND d.trans_date>='$start_date' AND d.trans_date<='$end_date'
            ) as debit"),
            DB::raw("(SELECT IF(SUM(e.credit) IS NULL,0, SUM(e.credit)) FROM vw_ledger e
            WHERE e.account_id=a.id AND a.company_id=e.company_id
            AND e.trans_date>='$start_date' AND e.trans_date<='$end_date'
            ) as credit"),
            DB::raw("(SELECT IF(SUM(f.debit) IS NULL,0, SUM(f.debit))+a.op_debit FROM vw_ledger f
            WHERE f.account_id=a.id AND a.company_id=f.company_id
            AND f.trans_date<'$start_date' AND f.trans_date>='$cutoff'
            ) as op_debit"),
            DB::raw("(SELECT IF(SUM(g.debit) IS NULL,0, SUM(g.debit))+a.op_debit FROM vw_ledger g
            WHERE g.account_id=a.id AND a.company_id=g.company_id
            AND g.trans_date<='$end_date' AND g.trans_date>='$cutoff'
            ) as total_debit"),
            DB::raw("(SELECT IF(SUM(h.credit) IS NULL,0, SUM(h.credit))+a.op_credit FROM vw_ledger h
            WHERE h.account_id=a.id AND a.company_id=h.company_id
            AND h.trans_date<'$start_date' AND h.trans_date>='$cutoff'
            ) as op_credit"),
            DB::raw("(SELECT IF(SUM(i.credit) IS NULL,0, SUM(i.credit))+a.op_credit FROM vw_ledger i
            WHERE i.account_id=a.id AND a.company_id=i.company_id
            AND i.trans_date<='$end_date' AND i.trans_date>='$cutoff'
            ) as total_credit"),
        ])
        ->whereRaw("a.company_id='$company->id' AND a.has_children=false AND account_group IN ('income', 'expense')");
        if(count($params['account_id'])>0){
            $income = $income->whereIn(DB::raw('a.id'), $params['account_id']);
        }
        $income = $income->orderBy(DB::raw('a.account_group'))->get();
        $balance = $balance->merge($income);
        return $balance;
    }

    private function getParams(Request $request, $company_id){
        $accounts = $request->query('accounts');
        $departments = $request->query('departments');

        // $zero = $request->query('zero', true);
        $zero = filter_var($request->zero, FILTER_VALIDATE_BOOLEAN);
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if((empty($start_date)  && empty($end_date))){
            $max_date = DB::table('vw_ledger')
            ->where('company_id', $company_id)->max('trans_date');
            if($max_date==null){
                $max_date = date('Y-m-d');
            }
            $start_date = \Carbon\Carbon::parse($max_date)->startOfMonth()->format('Y-m-d');
            $end_date = $max_date;
        }
        $params = [
            'account_id'=>$accounts??[],
            'department_id'=>$departments??[],
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'zero'=>$zero
        ];
        return $params;
    }

    private function getCutoffDate($date, $closingBooks){
        if(count($closingBooks)==0){
            return null;
        }
        foreach($closingBooks as $c){
            if($date<=$c->end_date && $date>=$c->start_date){
                return \Carbon\Carbon::parse($c->start_date)->addDay()->format('Y-m-d');
            }
        }
        return \Carbon\Carbon::parse(($closingBooks[0])->end_date)->addDay()->format('Y-m-d');
    }

}
