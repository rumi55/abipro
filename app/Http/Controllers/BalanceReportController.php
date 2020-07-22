<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Department;
use App\Http\Resources\BalanceReportResource;
use App\Http\Resources\DepartmentResource;
use DB;

class BalanceReportController extends Controller
{
    public function index(Request $request){
        $company = \Auth::user()->activeCompany();
        $company_id = $company->id;
        $params = $this->getParams($request, $company_id);
        if($params['compare']=='department'){
            $accounts = $this->query2($params, $company);
        }else{
            $accounts = $this->query($params, $company);
        }

        $title = 'Laporan Neraca';
        $view = 'report.balance.default';
        $departments = Department::whereIn('id', $params['department_id'])->get();


        $data = array(
            'report'=>'balance',
            'title'=>$title,
            'company'=>$company,
            'departments'=>$departments,
            'columns'=>$params['columns'],
            'compare'=>$params['compare'],
            'subaccount'=>$params['subaccount'],
            'accounts'=>$accounts,
            'view'=>$view
        );

        if(isset($request->output)){
            $output = $request->output;
            $data = array_merge($data, $params);
            if($output=='pdf'){
                return $this->pdf($view, $data);
            }else if($output=='print'){
                return $this->print($data);
            }else{
                return $this->html($data);
            }
        }else{
            return $this->html($view, $data);
        }

    }
    private function html($view, $data){
        return view('report.viewer', $data);
    }

    private function pdf($view, $data){
        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $c = count($data['columns']);
        if($c>3){
            $pdf->setPaper('a4', 'landscape');
        }
// return view('report.pdf', $data);
        $pdf->loadView('report.pdf', $data);
        return $pdf->stream($data['report'].'.pdf');
    }
    private function query($params, $company){
        $select = [
            DB::raw('a.id,  a.sequence, a.account_no, account_types.name as account_type, a.account_type_id, account_types.group AS account_group, a.account_name, a.tree_level')
        ];
        $departments = null;
        $dept_id = null;
        $department_id = $params['department_id'];
        if(!empty($params['department_id'])){
            $dept_id = implode(',',$params['department_id']);
            $departments = " AND tb.department_id IN ($dept_id) ";
        }

        foreach($params['columns'] as $i=>$column){
            $end_date = $column['end_date'];
            $select[] = DB::raw (
            "(SELECT IF(SUM(tb.total) iS NULL, 0, SUM(tb.total))
            FROM vw_transaction_balance tb WHERE tb.`trans_date`<='$end_date' AND  (tb.account_id=a.id OR tb.account_parent_id=a.id) $departments
            )+vw_opening_balance.balance as total_$i");
        }
        $balance = DB::table(DB::raw('accounts a'))
        ->select($select)
        ->join('account_types', 'account_types.id','=','account_type_id')
        ->leftJoin('vw_opening_balance', function($join)use($department_id){
            if(count($department_id)==0){
                $join->on('vw_opening_balance.account_id', '=', 'a.id')->whereNull('department_id');
            }else{
                $join->on('vw_opening_balance.account_id', '=', 'a.id')->whereIn('vw_opening_balance.department_id', $department_id);
            }

        })
        ->whereRaw("a.company_id='$company->id'")
        ->whereIn('group',['asset', 'liability', 'equity'])
        ->distinct()
        ->orderBy('a.sequence')->get();
        return $balance;
    }


    private function query2($params, $company){
        $select = [
            DB::raw('a.id,  a.sequence, a.account_no, account_types.name as account_type, a.account_type_id, account_types.group AS account_group, a.account_name, a.tree_level')
        ];

        foreach($params['columns'] as $i=>$header){
            $date= $params['year'].'-'.$params['end_month'].'-31';
            $plus = '';
            $id = $header->id;
            if($id===null & $header->name==null){//tanpa department
                $plus = '+a.balance';
                $select[] = DB::raw ("(SELECT IF(SUM(tb.total) IS NULL, 0,SUM(tb.total))
                FROM vw_transaction_balance tb WHERE tb.`trans_date`<='$date' AND  tb.company_id=a.company_id
                and a.id=tb.account_id AND department_id IS NULL) as total_$i");
            }else if($id===null & $header->name!=null){//total
                $plus = '+a.balance';
                $select[] = DB::raw ("(SELECT IF(SUM(tb.total) IS NULL, 0,SUM(tb.total))
                FROM vw_transaction_balance tb WHERE tb.`trans_date`<='$date' AND  tb.company_id=a.company_id
                and a.id=tb.account_id) as total_$i");
            }else{
                $select[] = DB::raw ("(SELECT IF(SUM(tb.total) IS NULL, 0,SUM(tb.total))
                FROM vw_transaction_balance tb WHERE tb.`trans_date`<='$date' AND  tb.company_id=a.company_id
                and a.id=tb.account_id AND department_id=$id) as total_$i");
            }
        }
        $balance = DB::table(DB::raw('accounts a'))
        ->select($select)
        ->join('account_types', 'account_types.id','=','account_type_id')
        ->leftJoin('vw_opening_balance', function($join){
            $join->on('vw_opening_balance.account_id', '=', 'a.id');
        })
        ->whereRaw("a.company_id='$company->id'")
        ->whereIn('group',['asset', 'liability', 'equity'])
        ->distinct()
        ->orderBy('a.sequence')->get();
        return $balance;
    }
    private function getParams(Request $request, $company_id){
        $departments = $request->query('departments', []);
        $compare = $request->compare??'period';
        $year = $request->year??date('Y');
        $start_month = $request->start_month??date('m');
        $end_month = $request->end_month??date('m');
        $total_year = filter_var($request->total_year, FILTER_VALIDATE_BOOLEAN);
        $total_last_year = filter_var($request->total_last_year, FILTER_VALIDATE_BOOLEAN);
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
                    'end_date'=>($year-1).'-12-31',
                    'label'=>$year-1,
                ];
            }
            for($month=$start_month;$month<=$end_month;$month++){
                $columns[] = [
                    'end_date'=>$year.'-'.$month.'-31',
                    'label'=>fmonth($year.'-'.$month.'-01'),
                ];
            }
            if($total_year){
                $columns[] = [
                    'end_date'=>$year.'-12-31',
                    'label'=>$year,
                ];
            }
        }

        $params = [
            'department_id'=>$departments,
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
        $cumulative = filter_var($request->cumulative, FILTER_VALIDATE_BOOLEAN);

        $subaccount = $request->query('subaccount', '0');

        //cut off date
        $cutoff_date = Company::find($company_id)->accounting_start_date;
        $cbook = \App\ClosingBook::whereDate('end_date', '<=', $end_date)->orderBy('end_date','desc')->first();

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
        }else{
            $sdate = $start_date;
            $edate = $end_date;
            $loop = $cumulative?($compare_period+1)*2:$compare_period+1;

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
                        $columns[$i] = ['start_date'=>null, 'end_date'=>$edate, 'label'=>'s.d '.(fdate($sdate, 'M Y'))];
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
            'department_id'=>$departments,
            'end_date'=>$end_date,
            'start_date'=>$start_date,
            'cutoff_date'=>$cutoff_date,
            'compare'=>$compare,
            'compare_period'=>$compare_period,
            'period'=>$period,
            'columns'=>$columns,
            'zero'=>$zero,
            'cumulative'=>$cumulative,
            'subaccount'=>$subaccount,
        ];

        return $params;
    }
    private function getParams2(Request $request, $company_id){
        $department_id = $request->departments;
        $departments = [];
        $paramsString = "";
        if(!empty($department_id)){
            $exploded = explode(',',$department_id);
            $paramsString.=($paramsString!=''?'&':'')."departments=$department_id";
            foreach($exploded as $id){
                $departments[] = decode($id);
            }
        }

        $period = $request->period;
        $end_date = $request->end_date;
        $compare = $request->compare;
        $zero = $request->query('zero', true);
        $subaccount = $request->query('subaccount', false);
        $level = $request->query('level', '1');

        if(!empty($end_date)){
            $paramsString.=($paramsString!=''?'&':'')."end_date=$end_date";
        }
        if(!empty($compare)){
            $paramsString.=($paramsString!=''?'&':'')."compare=$compare";
        }
        if(!empty($period)){
            $paramsString.=($paramsString!=''?'&':'')."period=$period";
        }
        $paramsString.=($paramsString!=''?'&':'')."zero=$zero";
        $paramsString.=($paramsString!=''?'&':'')."subaccount=$subaccount&level=$level";
        $cols = array();
        if($compare=='mtd1'){
            $sdate=\Carbon\Carbon::parse($end_date);
            $edate=\Carbon\Carbon::parse($end_date)->startOfMonth();
            $cols[0] = $end_date;//$sdate->endOfYear()->format('Y-m-d');
            $cols[1] = $end_date;//bulan berjalan
            $cols[2] = $edate->subMonth()->endOfMonth()->format('Y-m-d');//bulan lalu
        }else if($compare=='mtd2'){
            $sdate=\Carbon\Carbon::parse($end_date);
            $edate=\Carbon\Carbon::parse($end_date)->startOfMonth();
            $cols[0] = $end_date;//$sdate->endOfYear()->format('Y-m-d');//tahun berjalan
            $cols[1] = $edate->subMonth()->endOfMonth()->format('Y-m-d');//bulan lalu
            $cols[2] = $sdate->subYear()->endOfYear()->format('Y-m-d');//tahun lalu
        }else if($compare=='department'){
            if(!empty($department_id)){
                $cols = Department::whereIn('id', $departments)->get();
            }else{
                $cols = Department::all();
                $cols->push(new Department());
                $all = new Department();
                $all->name = 'Jumlah';
                $cols->push($all);
            }
        }else{
            $date = \Carbon\Carbon::parse($end_date);
            for($i=0;$i<$period+1;$i++){
                if($i==0){
                    $cols[$i] = $end_date;
                }else{
                    if($compare=='weekly'){
                        $cols[$i] = $date->subWeek()->format('Y-m-d');
                    }else if($compare=='monthly' || $compare=='monthly_report'){
                        if($i==1){
                            $sdate=$date->startOfMonth();
                        }else{
                            $sdate=$sdate->startOfMonth();
                        }
                        $cols[$i] = $sdate->subMonth()->endOfMonth()->format('Y-m-d');
                    }else if($compare=='quarterly'){
                        if($i==1){
                            $sdate=$date->startOfMonth();
                        }else{
                            $sdate=$sdate->startOfMonth();
                        }
                        $cols[$i] = $sdate->subQuarter()->endOfMonth()->format('Y-m-d');
                    }else if($compare=='semi-yearly'){
                        if($i==1){
                            $sdate=$date->startOfMonth();
                        }else{
                            $sdate=$sdate->startOfMonth();
                        }
                        $cols[$i] = $sdate->subMonths(6)->endOfMonth()->format('Y-m-d');
                    }else if($compare=='yearly'){
                        if($i==1){
                            $sdate=$date->endOfYear();
                        }
                        $cols[$i] = $sdate->subYear()->format('Y-m-d');
                    }
                }
            }
        }

        $columns = array();
        if($compare=='department'){
            $columns = $cols;
        }else{
            for($i=count($cols)-1;$i>=0;$i--){
                $columns[]=$cols[$i];
            }
        }

        $params = [
            'params'=>$paramsString,
            'department_id'=>$departments,
            'end_date'=>$end_date,
            'compare'=>$compare,
            'columns'=>$columns,
            'zero'=>$zero,
            'subaccount'=>$subaccount,
            'level'=>$level
        ];
        return $params;
    }
}
