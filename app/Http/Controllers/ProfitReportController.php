<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Department;
use App\Http\Resources\ProfitReportResource;
use App\Http\Resources\DepartmentResource;
use DB;
use Auth;

class ProfitReportController extends Controller
{
    public function index(Request $request){
        $company = Auth::user()->activeCompany();
        $company_id = $company->id;
        $params = $this->getParams($request, $company_id);

        if($params['compare']=='department'){
            $income = $this->query2($params, $company);
        }else if($params['compare']=='budget'){
            $income = $this->query3($params, $company);
        }else{
            $income = $this->query($params, $company);
        }

        $title = trans('Profit & Loss');
        $view = 'report.profit.default';
        $departments = Department::whereIn('id', $params['department_id'])->get();

        $data = array(
            'report'=>'profit',
            'title'=>$title,
            'company'=>$company,
            'departments'=>$departments,
            'compare'=>$params['compare'],
            'subaccount'=>$params['subaccount'],
            'columns'=>$params['columns'],
            'income'=>$income,
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
    private function print($data){
        return view('report.print',$data);
    }
    private function pdf($view, $data){
        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $c = count($data['columns']);
        if($c>3){
            $pdf->setPaper('a4', 'landscape');
        }
        $pdf->loadView('report.pdf', $data);
        return $pdf->stream('profit.pdf');
    }
    private function query($params, $company){
        $select = [
            DB::raw('a.id,  a.sequence, a.account_no, account_types.name as account_type, a.account_type_id, account_types.group AS account_group, a.account_name, a.tree_level')
        ];
        // $cutoff_date = $params['cutoff_date'];
        // dd($cutoff_date);
        foreach($params['columns'] as $i=>$column){
            $start_date = $column['start_date'];
            $end_date = $column['end_date'];
            $departments = '';
            if(!empty($params['department_id'])){
                $dept_id = implode(',',$params['department_id']);
                $departments = " AND tb.department_id IN ($dept_id) ";
                $plus = '';
            }
            if($start_date==null){//kumulative
                $select[] = DB::raw (
                    "(SELECT IF(SUM(tb.total) iS NULL, 0, SUM(tb.total))
                    FROM vw_transaction_balance tb WHERE tb.`trans_date`<='$end_date' AND  (tb.account_id=a.id OR tb.account_parent_id=a.id) $departments
                    ) as total_$i");
            }else{
                $select[] = DB::raw (
                    "(SELECT IF(SUM(tb.total) iS NULL, 0, SUM(tb.total))
                    FROM vw_transaction_balance tb WHERE tb.`trans_date`>='$start_date' AND tb.`trans_date`<='$end_date' AND  (tb.account_id=a.id OR tb.account_parent_id=a.id) $departments
                    ) as total_$i");
            }
        }
        $income = DB::table(DB::raw('accounts a'))
        ->select($select)
        ->join('account_types', 'account_types.id','=','account_type_id')
        ->leftJoin('vw_opening_balance', function($join){
            $join->on('vw_opening_balance.account_id', '=', 'a.id')->whereNull('department_id');
        })
        ->whereRaw("a.company_id='$company->id'")
        ->whereIn('account_types.id',[10,12,13,15, 16,17,18])
        ->distinct()
        ->orderBy('a.sequence')->get();
        return $income;
    }

    private function query2($params, $company){

        $select = [
            DB::raw('a.id,  a.sequence, a.account_no, account_types.name as account_type, a.account_type_id, account_types.group AS account_group, a.account_name, a.tree_level')
        ];

        $start_date=($params['year']).'-'.($params['start_month']).'-01';
        $end_date=($params['year']).'-'.($params['end_month']).'-31';
        foreach($params['columns'] as $i=>$header){
            $id = $header->id;
            if($id===null & $header->name==null){//tanpa department
                $select[] = DB::raw (
                    "(SELECT IF(SUM(tb.total) iS NULL, 0, SUM(tb.total))
                    FROM vw_transaction_balance tb WHERE tb.`trans_date`>='$start_date' AND tb.`trans_date`<='$end_date'
                    AND  (tb.account_id=a.id OR tb.account_parent_id=a.id)  AND department_id IS NULL
                    ) as total_$i");
            }else if($id===null & $header->name!=null){//total
                $select[] = DB::raw (
                    "(SELECT IF(SUM(tb.total) iS NULL, 0, SUM(tb.total))
                    FROM vw_transaction_balance tb WHERE tb.`trans_date`>='$start_date' AND tb.`trans_date`<='$end_date'
                    AND  (tb.account_id=a.id OR tb.account_parent_id=a.id)
                    ) as total_$i");
            }else{
                $select[] = DB::raw (
                    "(SELECT IF(SUM(tb.total) iS NULL, 0, SUM(tb.total))
                    FROM vw_transaction_balance tb WHERE tb.`trans_date`>='$start_date' AND tb.`trans_date`<='$end_date'
                    AND  (tb.account_id=a.id OR tb.account_parent_id=a.id) AND department_id=$id
                    ) as total_$i");
            }
        }
        $income = DB::table(DB::raw('accounts a'))
        ->select($select)
        ->join('account_types', 'account_types.id','=','account_type_id')
        ->leftJoin('vw_opening_balance', function($join){
            $join->on('vw_opening_balance.account_id', '=', 'a.id')->whereNull('department_id');
        })
        ->whereRaw("a.company_id='$company->id'")
        ->whereIn('account_types.id',[10,12,13,15, 16,17,18])
        ->distinct()
        ->orderBy('a.sequence')->get();
        return $income;
    }
    private function query3($params, $company){
        $select = [
            DB::raw('a.id,  a.sequence, a.account_no, account_types.name as account_type, a.account_type_id, account_types.group AS account_group, a.account_name, a.tree_level')
        ];
        // $cutoff_date = $params['cutoff_date'];
        // dd($cutoff_date);
        $month_name = ['total', 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        foreach($params['columns'] as $i=>$column){
            $start_date = $column['start_date'];
            $end_date = $column['end_date'];
            $month = intval(fdate($start_date, 'm'));
            $year = fdate($start_date, 'Y');
            $departments = '';
            if(!empty($params['department_id'])){
                $dept_id = implode(',',$params['department_id']);
                $departments = " AND tb.department_id IN ($dept_id) ";
            }
            if($start_date==null){//kumulative
                $select[] = DB::raw (
                    "(SELECT IF(SUM(tb.total) iS NULL, 0, SUM(tb.total))
                    FROM vw_transaction_balance tb WHERE tb.`trans_date`<='$end_date' AND  (tb.account_id=a.id OR tb.account_parent_id=a.id) $departments
                    ) as total_$i");
                $select[] = DB::raw (
                    "(SELECT total
                    FROM vw_budgets tb WHERE budget_year=$year AND tb.account_id=a.id $departments
                    ) as budget_$i");
            }else{
                $select[] = DB::raw (
                    "(SELECT IF(SUM(tb.total) iS NULL, 0, SUM(tb.total))
                    FROM vw_transaction_balance tb WHERE tb.`trans_date`>='$start_date' AND tb.`trans_date`<='$end_date' AND  (tb.account_id=a.id OR tb.account_parent_id=a.id) $departments
                    ) as total_$i");
                $select[] = DB::raw (
                    "(SELECT $month_name[$month]
                    FROM vw_budgets tb WHERE budget_year=$year AND tb.account_id=a.id $departments
                    ) as budget_$i");
            }
        }
        $income = DB::table(DB::raw('accounts a'))
        ->select($select)
        ->join('account_types', 'account_types.id','=','account_type_id')
        ->leftJoin('vw_opening_balance', function($join){
            $join->on('vw_opening_balance.account_id', '=', 'a.id')->whereNull('department_id');
        })
        ->whereRaw("a.company_id='$company->id'")
        ->whereIn('account_types.id',[10,12,13,15, 16,17,18])
        ->distinct()
        ->orderBy('a.sequence')->get();
        return $income;
    }
    private function query4($params, $company){
        $select = [
            DB::raw('a.id, a.account_no,a.sequence, a.account_type, a.account_type_id, a.account_group, a.account_name, a.tree_level')
        ];

        foreach($params['columns'] as $i=>$column){
            $start_date = $column['start_date'];
            $end_date = $column['end_date'];
            $month = fdate($start_date, 'Y-m');
            $year = fdate($start_date, 'Y');
            if($i==1){
                $select[] = DB::raw ("(SELECT IF(SUM(tb.budget) IS NULL, 0,SUM(tb.budget))
                FROM vw_budgets tb WHERE tb.`budget_month`='$month' AND  tb.company_id=a.company_id
                and a.id=tb.account_id) as total_$i");
            }else if($i==3){
                $select[] = DB::raw ("(SELECT IF(SUM(tb.budget) IS NULL, 0,SUM(tb.budget))
                FROM vw_budgets tb WHERE tb.`budget_year`='$year' AND  tb.company_id=a.company_id
                and a.id=tb.account_id) as total_$i");
            }else{
                $select[] = DB::raw ("(SELECT IF(SUM(tb.total) IS NULL, 0,SUM(tb.total))
                FROM vw_balance tb WHERE tb.`trans_date`>='$start_date' AND tb.`trans_date`<='$end_date' AND  tb.company_id=a.company_id
                and a.id=tb.account_id) as total_$i");
            }
        }
        $income = DB::table(DB::raw('vw_accounts a'))
        ->select($select)
        ->whereRaw("a.company_id=$company->id AND (a.type IS NULL OR a.type<>'tax')")
        ->whereIn('a.account_type_id',[12,14,15])
        ->distinct()
        ->orderBy(DB::raw('a.account_type_id, a.sequence'))->get();
        $other_income = DB::table(DB::raw('vw_accounts a'))
        ->select($select)
        ->whereRaw("a.company_id='$company->id'")
        ->whereIn('a.account_type_id', [13, 16])
        ->distinct()
        ->orderBy(DB::raw('a.account_type_id, a.sequence'))->get();
        $income_tax = DB::table(DB::raw('vw_accounts a'))
        ->select($select)
        ->whereRaw("a.company_id='$company->id' AND a.type='income_tax'")
        ->distinct()
        ->orderBy(DB::raw('a.account_type_id, a.sequence'))->get();
        return [$income, $other_income, $income_tax];
    }

    private function getParams(Request $request, $company_id){
        $departments = $request->query('departments', []);
        $compare = $request->compare??'period';
        $year = $request->year??date('Y');
        $start_month = $request->start_month??date('m');
        $end_month = $request->end_month??date('m');
        $total_year = filter_var($request->total_year, FILTER_VALIDATE_BOOLEAN);
        $total_last_year = filter_var($request->total_last_year, FILTER_VALIDATE_BOOLEAN);
        $cumulative = filter_var($request->cumulative, FILTER_VALIDATE_BOOLEAN);
        $subaccount = $request->subaccount??0;
        $columns = array();
        if($compare=='department'){
            if(!empty($departments)){
                $columns = Department::where('company_id', $company_id)->whereIn('id', $departments)->get();
            }else{
                $columns = Department::where('company_id', $company_id)->get();
                $columns->push(new Department());
                $all = new Department();
                $all->name = 'Jumlah';
                $columns->push($all);
            }
        }else{
            if($total_last_year){
                $columns[] = [
                    'start_date'=>($year-1).'-01-01',
                    'end_date'=>($year-1).'-12-31',
                    'label'=>$year-1,
                ];
            }
            for($month=$start_month;$month<=$end_month;$month++){
                $columns[] = [
                    'start_date'=>$year.'-'.$month.'-01',
                    'end_date'=>$year.'-'.$month.'-31',
                    'label'=>fmonth($year.'-'.$month.'-01'),
                ];
            }
            if($total_year){
                $columns[] = [
                    'start_date'=>$year.'-01-01',
                    'end_date'=>$year.'-12-31',
                    'label'=>$year,
                ];
            }
        }

        $params = [
            'department_id'=>$departments,
            'cumulative'=>$cumulative,
            'year'=>$year,
            'start_month'=>$start_month,
            'end_month'=>$end_month,
            'compare'=>$compare,
            'columns'=>$columns,
            'subaccount'=>$subaccount,
        ];
        return $params;
    }

    private function getParamsOld(Request $request, $company_id){
        $departments = $request->query('departments', []);
        $compare = $request->query('compare', 'period');
        $period = $request->query('period', 'monthly');
        $compare_period = $request->query('compare_period',0);
        $start_date = $request->query('start_date', date('Y-m-d'));
        $end_date = $start_date;

        $zero = filter_var($request->zero, FILTER_VALIDATE_BOOLEAN);
        $show_total = filter_var($request->show_total, FILTER_VALIDATE_BOOLEAN);
        $total_year = filter_var($request->total_year, FILTER_VALIDATE_BOOLEAN);
        $cumulative = filter_var($request->cumulative, FILTER_VALIDATE_BOOLEAN);

        $subaccount = $request->query('subaccount', '0');

        //cut off date
        $cutoff_date = Company::find($company_id)->accounting_start_date;

        $cbook = \App\ClosingBook::whereDate('end_date', '<=', $end_date)->orderBy('end_date','desc')->first();
        $cbooks = \App\ClosingBook::where('company_id', $company_id)->orderBy('end_date','desc')->get();

        if($cbook!=null){
            $co_date = $cbook->end_date;
            $cutoff_date = \Carbon\Carbon::parse($co_date)->addDay()->format('Y-m-d');
        }

        $columns = array();
        if($period=='daily'){
            $start_date = $end_date;
        }else if($period=='weekly'){
            $start_date = \Carbon\Carbon::parse($end_date)->startOfWeek()->format('Y-m-d');
            $end_date = \Carbon\Carbon::parse($end_date)->endOfWeek()->format('Y-m-d');
        }else if($period=='monthly'){
            $start_date = \Carbon\Carbon::parse($end_date)->startOfMonth()->format('Y-m-d');
            $end_date = \Carbon\Carbon::parse($end_date)->endOfMonth()->format('Y-m-d');
        }else if($period=='quarterly'){
            $start_date = \Carbon\Carbon::parse($end_date)->startOfMonth()->format('Y-m-d');
            $end_date = \Carbon\Carbon::parse($end_date)->addMonth(2)->endOfMonth()->format('Y-m-d');
        }else if($period=='semiyearly'){
            $start_date = \Carbon\Carbon::parse($end_date)->startOfMonth()->format('Y-m-d');
            $end_date = \Carbon\Carbon::parse($end_date)->addMonth(5)->endOfMonth()->format('Y-m-d');
        }else if($period=='yearly'){
            $start_date = \Carbon\Carbon::parse($end_date)->startOfYear()->format('Y-m-d');
            $end_date = \Carbon\Carbon::parse($end_date)->endOfYear()->format('Y-m-d');
        }
        if($compare=='department'){
            if(!empty($departments)){
                $columns = Department::where('company_id', $company_id)->whereIn('id', $departments)->get();
            }else{
                $columns = Department::where('company_id', $company_id)->get();
                $columns->push(new Department());
                $all = new Department();
                $all->name = 'Jumlah';
                $columns->push($all);
            }
        }else if($compare=='budget'){
            $sdate = $start_date;
            $edate = $end_date;
            $columns[0] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'M Y')];
            $columns[1] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>'Anggaran '.fdate($sdate, 'M Y')];
            $start_date = \Carbon\Carbon::parse($end_date)->startOfYear()->format('Y-m-d');
            $end_date = \Carbon\Carbon::parse($end_date)->endOfYear()->format('Y-m-d');
            $columns[2] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'Y')];
            $columns[3] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>'Anggaran '.fdate($sdate, 'Y')];
        }else{
            $sdate = $start_date;
            $edate = $end_date;
            $loop = $cumulative?($compare_period+1)*2:$compare_period+1;
            $loop = $cumulative?($compare_period+1)*2:$compare_period+1;
            $cdate = '';//cutoff date in start date and end date range
            if($period=='daily'){
                for($i=0;$i<$loop;$i++){
                    if($cumulative){
                        $columns[$i] = ['start_date'=>null, 'end_date'=>$edate, 'label'=>'s.d '.(fdate($sdate, 'd-m-Y'))];
                        $columns[++$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'd-m-Y')];
                    }else{
                        $columns[$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'd-m-Y')];
                    }
                    $sdate = \Carbon\Carbon::parse($sdate)->subDay()->format('Y-m-d');
                    $edate = $sdate;
                }
            }else if($period=='weekly'){
                for($i=0;$i<$loop;$i++){
                    if($cumulative){
                        $columns[$i] = ['start_date'=>null, 'end_date'=>$edate, 'label'=>'s.d '.(fdate($edate, 'd-m-Y'))];
                        $columns[++$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'd-m-Y').' s.d '.fdate($edate, 'd-m-Y')];
                    }else{
                        $columns[$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'd-m-Y').' s.d '.fdate($edate, 'd-m-Y')];
                    }
                    $sdate = \Carbon\Carbon::parse($sdate)->subWeek()->format('Y-m-d');
                    $edate = \Carbon\Carbon::parse($edate)->subWeek()->format('Y-m-d');
                }
            }else if($period=='monthly'){
                for($i=0;$i<$loop;$i++){
                    if($cumulative){
                        $cutoff = $this->getCutoffDate($edate, $cbooks);
                        $columns[$i] = ['start_date'=>$cutoff, 'end_date'=>$edate, 'label'=>(fdate($cutoff, 'M Y')).' s.d '.(fdate($sdate, 'M Y'))];
                        $columns[++$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'M Y')];
                    }else{
                        $columns[$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'M Y')];
                    }
                    $sdate = \Carbon\Carbon::parse($sdate)->subMonth()->format('Y-m-d');
                    $edate = \Carbon\Carbon::parse($sdate)->endOfMonth()->format('Y-m-d');
                }
            }else if($period=='quarterly'){
                for($i=0;$i<$loop;$i++){
                    if($cumulative){
                        $columns[$i] = ['start_date'=>null, 'end_date'=>$edate, 'label'=>'s.d '.(fdate($edate, 'M Y'))];
                        $columns[++$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'M Y').' - '.fdate($edate, 'M Y')];
                    }else{
                        $columns[$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'M Y').' - '.fdate($edate, 'M Y')];
                    }
                    $edate = \Carbon\Carbon::parse($sdate)->subDay()->format('Y-m-d');
                    $sdate = \Carbon\Carbon::parse($edate)->startOfMonth()->subMonth(2)->format('Y-m-d');
                }
            }else if($period=='semiyearly'){
                for($i=0;$i<$loop;$i++){
                    if($cumulative){
                        $columns[$i] = ['start_date'=>null, 'end_date'=>$edate, 'label'=>'s.d '.(fdate($edate, 'M Y'))];
                        $columns[++$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'M Y').' - '.fdate($edate, 'M Y')];
                    }else{
                        $columns[$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'M Y').' - '.fdate($edate, 'M Y')];
                    }
                    $edate = \Carbon\Carbon::parse($sdate)->subDay()->format('Y-m-d');
                    $sdate = \Carbon\Carbon::parse($edate)->startOfMonth()->subMonth(5)->format('Y-m-d');
                }
            }else if($period=='yearly'){
                for($i=0;$i<$loop;$i++){
                    $columns[$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'Y')];
                    $sdate = \Carbon\Carbon::parse($sdate)->subYear()->format('Y-m-d');
                    $edate = \Carbon\Carbon::parse($edate)->subYear()->format('Y-m-d');
                }
            }
            $columns = array_reverse($columns);
        }

        $params = [
            // 'params'=>$paramsString,
            'department_id'=>$departments,
            'cutoff_date'=>$cutoff_date,
            'cumulative'=>$cumulative,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'compare'=>$compare,
            'compare_period'=>$compare_period,
            'period'=>$period,
            'columns'=>$columns,
            'zero'=>$zero,
            'subaccount'=>$subaccount
        ];
        // dd($columns);
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
