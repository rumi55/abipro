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
            $accounts = $this->query2($params, $company);
        }else if($params['compare']=='budget'){
            $accounts = $this->query3($params, $company);
        }else{
            $accounts = $this->query($params, $company);
        }
        
        $title = 'Laporan Laba-Rugi';
        $view = 'report.profit.default';
        $period = fdate($params['end_date'], 'd M Y');
        $balance_date = \Carbon\Carbon::parse($params['end_date'])->subDay()->format('d-m-Y');
        $departments = Department::whereIn('id', $params['department_id'])->get();
        
        $data = array(
            'report'=>'profit',
            'title'=>$title,
            'company'=>$company,
            'departments'=>$departments,
            'compare'=>$params['compare'],
            'subaccount'=>$params['subaccount'], 
            'columns'=>$params['columns'],
            'income'=>$accounts[0],
            'other_income'=>$accounts[1],
            'income_tax'=>$accounts[2],
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
            DB::raw('a.id, a.sequence, a.account_no, a.account_type, a.account_type_id, a.account_group, a.account_name, a.tree_level')
        ];
        $cutoff_date = $params['cutoff_date'];
        foreach($params['columns'] as $i=>$column){
            $start_date = $column['start_date'];
            $end_date = $column['end_date'];
            $departments = '';
            if(!empty($params['department_id'])){
                $dept_id = implode(',',$params['department_id']);
                $departments = " AND c$i.department_id IN ($dept_id) ";
                $plus = '';
            }
            if($start_date==null){
                $select[] = DB::raw ("(SELECT IF(SUM(c$i.total) IS NULL, 0,SUM(c$i.total))
                FROM vw_balance c$i WHERE c$i.`trans_date`<='$end_date' AND  c$i.company_id=a.company_id 
                and a.id=c$i.account_id $departments) as total_$i");
            }else{
                $select[] = DB::raw ("(SELECT IF(SUM(c$i.total) IS NULL, 0,SUM(c$i.total))
                FROM vw_balance c$i WHERE c$i.`trans_date`>='$start_date' AND c$i.`trans_date`<='$end_date' AND  c$i.company_id=a.company_id 
                and a.id=c$i.account_id $departments) as total_$i");
            }
        }
        
        // DB::enableQueryLog();
        $income = DB::table(DB::raw('vw_accounts a'))
        ->select($select)
        ->whereRaw("a.company_id=$company->id AND (a.type IS NULL OR a.type<>'tax')")
        ->whereIn('a.account_type_id',[12,14,15])
        ->distinct()
        ->orderBy(DB::raw('a.account_type_id, a.sequence'))->get();
        // dd(DB::getQueryLog());
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

    private function query2($params, $company){
        
        $select = [
            DB::raw('a.id, a.account_no, a.sequence, a.account_type, a.account_type_id, a.account_group, a.account_name, a.tree_level')
        ];
        
        foreach($params['columns'] as $i=>$header){
            $end_date= $params['end_date'];
            $start_date= $params['start_date'];
            $id = $header->id;
            if($id===null & $header->name==null){//tanpa department
                $select[] = DB::raw ("(SELECT IF(SUM(c$i.total) IS NULL, 0,SUM(c$i.total))
                FROM vw_balance c$i WHERE c$i.`trans_date`>='$start_date' AND c$i.`trans_date`<='$end_date' AND  c$i.company_id=a.company_id 
                and a.id=c$i.account_id AND department_id IS NULL) as total_$i");
            }else if($id===null & $header->name!=null){//total 
                $select[] = DB::raw ("(SELECT IF(SUM(c$i.total) IS NULL, 0,SUM(c$i.total))
                FROM vw_balance c$i WHERE c$i.`trans_date`>='$start_date' AND c$i.`trans_date`<='$end_date' AND  c$i.company_id=a.company_id 
                and a.id=c$i.account_id) as total_$i");     
            }else{
                $select[] = DB::raw ("(SELECT IF(SUM(c$i.total) IS NULL, 0,SUM(c$i.total))
                FROM vw_balance c$i WHERE c$i.`trans_date`>='$start_date' AND c$i.`trans_date`<='$end_date' AND  c$i.company_id=a.company_id 
                and a.id=c$i.account_id AND department_id=$id) as total_$i");     
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
    private function query3($params, $company){
        $select = [
            DB::raw('a.id, a.account_no,a.sequence, a.account_type, a.account_type_id, a.account_group, a.account_name, a.tree_level')
        ];
        
        foreach($params['columns'] as $i=>$column){
            $start_date = $column['start_date'];
            $end_date = $column['end_date'];
            $month = fdate($start_date, 'Y-m');
            $year = fdate($start_date, 'Y');
            if($i==1){
                $select[] = DB::raw ("(SELECT IF(SUM(c$i.budget) IS NULL, 0,SUM(c$i.budget))
                FROM vw_budgets c$i WHERE c$i.`budget_month`='$month' AND  c$i.company_id=a.company_id 
                and a.id=c$i.account_id) as total_$i");
            }else if($i==3){
                $select[] = DB::raw ("(SELECT IF(SUM(c$i.budget) IS NULL, 0,SUM(c$i.budget))
                FROM vw_budgets c$i WHERE c$i.`budget_year`='$year' AND  c$i.company_id=a.company_id 
                and a.id=c$i.account_id) as total_$i");
            }else{
                $select[] = DB::raw ("(SELECT IF(SUM(c$i.total) IS NULL, 0,SUM(c$i.total))
                FROM vw_balance c$i WHERE c$i.`trans_date`>='$start_date' AND c$i.`trans_date`<='$end_date' AND  c$i.company_id=a.company_id 
                and a.id=c$i.account_id) as total_$i");
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

    private function queryChart($params, $company){
        $select = [
            DB::raw('a.id')
        ];
        foreach($params['columns'] as $i=>$column){
            $start_date = $column['start_date'];
            $end_date = $column['end_date'];
            $departments = '';
            if(!empty($params['department_id'])){
                $dept_id = implode(',',$params['department_id']);
                $departments = " AND c$i.department_id IN ($dept_id) ";
                $plus = '';
            }
            $select[] = DB::raw ("(SELECT IF(SUM(c$i.total) IS NULL, 0,SUM(c$i.total))
            FROM vw_balance c$i WHERE c$i.`trans_date`>='$start_date' AND c$i.`trans_date`<='$end_date' 
            and c$i.account_type_id=12)-
            (SELECT IF(SUM(c$i.total) IS NULL, 0,SUM(c$i.total))
            FROM vw_balance c$i WHERE c$i.`trans_date`>='$start_date' AND c$i.`trans_date`<='$end_date' 
            and c$i.account_type_id=14 and c$i.company_id='$company->id')
            as total_$i");
        }
        $balance = DB::table(DB::raw('companies a'))
        ->select($select)
        ->where('a.id', $company->id)
        ->distinct()->first();
        return $balance;
    }


    private function getParams(Request $request, $company_id){
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
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'compare'=>$compare,
            'compare_period'=>$compare_period,
            'period'=>$period,
            'columns'=>$columns,
            'zero'=>$zero,
            'cumulative'=>$cumulative,
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
