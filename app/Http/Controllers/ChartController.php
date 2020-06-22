<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Department;
use App\Http\Resources\ProfitReportResource;
use App\Http\Resources\DepartmentResource;
use DB;
use Auth;

class ChartController extends Controller
{
    public function profit(Request $request){
        $company = Auth::user()->activeCompany();
        $company_id = $company->id;$params = $this->getParams($request, $company_id);
        $data = $this->profitQuery($params, $company);
        
        $chart_data = array();
        foreach($params['columns'] as $i => $column){
            $total = 'total_'.$i;
            $chart_data[] = [
                'x'=>$column['label'],
                'y'=>$data->$total
            ];
        }

        return response()->json(['data'=>$chart_data]);
    }
    public function cashflow(Request $request){
        $type = $request->type;
        $company = Auth::user()->activeCompany();
        $company_id = $company->id;$params = $this->getParams($request, $company_id);
        $data = $this->cashflowQuery($type, $params, $company);
        
        $chart_data = array();
        foreach($params['columns'] as $i => $column){
            $total = 'total_'.$i;
            $chart_data[] = [
                'x'=>$column['label'],
                'y'=>$data->$total
            ];
        }

        return response()->json(['data'=>$chart_data]);
    }
    public function expense(Request $request){
        $company = Auth::user()->activeCompany();
        $company_id = $company->id;$params = $this->getParams($request, $company_id);
        $data = $this->expenseQuery($params, $company);
        return response()->json(['data'=>$data]);
    }

    private function profitQuery($params, $company){
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
        $data = DB::table(DB::raw('companies a'))
        ->select($select)
        ->where('a.id', $company->id)
        ->distinct()->first();
        return $data;
    }
    private function cashflowQuery($type,$params, $company){
        $select = [
            DB::raw('a.id')
        ];
        $total = $type=='in'?'debit':'credit';
        foreach($params['columns'] as $i=>$column){
            $start_date = $column['start_date'];
            $end_date = $column['end_date'];
            $departments = '';
            if(!empty($params['department_id'])){
                $dept_id = implode(',',$params['department_id']);
                $departments = " AND c$i.department_id IN ($dept_id) ";
                $plus = '';
            }
            $select[] = DB::raw ("(SELECT IF(SUM(c$i.$total) IS NULL, 0,SUM(c$i.$total))
            FROM vw_balance c$i WHERE c$i.`trans_date`>='$start_date' AND c$i.`trans_date`<='$end_date' 
            and c$i.account_type_id=1) as total_$i");
        }
        $data = DB::table(DB::raw('companies a'))
        ->select($select)
        ->where('a.id', $company->id)
        ->distinct()->first();
        return $data;
    }
    private function expenseQuery($params, $company){
        $i = count($params['columns'])-1;
        $start_date = $params['columns'][$i]['start_date'];
        $end_date = $params['columns'][$i]['end_date'];
        $data = DB::table('vw_balance')
        ->selectRaw("account_name as x, IF(SUM(total) IS NULL, 0,SUM(total)) as y")
        ->where('company_id', $company->id)
        ->whereRaw("`trans_date`>='$start_date' AND `trans_date`<='$end_date'")
        ->where('account_type_id', 15)
        ->where('tree_level', 0)
        ->groupBy('account_name')
        ->get();
        return $data;
    }


    private function getParams(Request $request, $company_id){
        $period = $request->period;
        $compare_period = 5;
        $now = date('Y-m-d');
        $start_date = $now;
        $end_date = $now;
        $columns = array();
        if($period=='daily'){
            $sdate = $start_date;
            $edate = $end_date;
            for($i=0;$i<$compare_period+1;$i++){
                $columns[$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'd-m-Y')];
                $sdate = \Carbon\Carbon::parse($sdate)->subDay()->format('Y-m-d'); 
                $edate = $sdate; 
            }
        }else if($period=='weekly'){
            $sdate = \Carbon\Carbon::parse($end_date)->startOfWeek()->format('Y-m-d');
            $edate = \Carbon\Carbon::parse($end_date)->endOfWeek()->format('Y-m-d');
            
            for($i=0;$i<$compare_period+1;$i++){
                $columns[$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'd-m-Y').' s.d '.fdate($edate, 'd-m-Y')];
                $sdate = \Carbon\Carbon::parse($sdate)->subWeek()->format('Y-m-d'); 
                $edate = \Carbon\Carbon::parse($edate)->subWeek()->format('Y-m-d'); 
            }
        }else if($period=='monthly'){
            $sdate = \Carbon\Carbon::parse($end_date)->startOfMonth()->format('Y-m-d');
            $edate = \Carbon\Carbon::parse($end_date)->endOfMonth()->format('Y-m-d');            
            for($i=0;$i<$compare_period+1;$i++){
                $columns[$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'M Y')];
                $sdate = \Carbon\Carbon::parse($sdate)->subMonth()->format('Y-m-d'); 
                $edate = \Carbon\Carbon::parse($sdate)->endOfMonth()->format('Y-m-d'); 
            }
        }else if($period=='quarterly'){
            $sdate = \Carbon\Carbon::parse($end_date)->startOfMonth()->format('Y-m-d');
            $edate = \Carbon\Carbon::parse($end_date)->addMonth(2)->endOfMonth()->format('Y-m-d');
            for($i=0;$i<$compare_period+1;$i++){
                $columns[$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'M Y').' - '.fdate($edate, 'M Y')];
                $edate = \Carbon\Carbon::parse($sdate)->subDay()->format('Y-m-d'); 
                $sdate = \Carbon\Carbon::parse($edate)->startOfMonth()->subMonth(2)->format('Y-m-d'); 
            }
        }else if($period=='semiyearly'){
            $sdate = \Carbon\Carbon::parse($end_date)->startOfMonth()->format('Y-m-d');
            $edate = \Carbon\Carbon::parse($end_date)->addMonth(5)->endOfMonth()->format('Y-m-d');
            for($i=0;$i<$compare_period+1;$i++){
                $columns[$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'M Y').' - '.fdate($edate, 'M Y')];
                $edate = \Carbon\Carbon::parse($sdate)->subDay()->format('Y-m-d'); 
                $sdate = \Carbon\Carbon::parse($edate)->startOfMonth()->subMonth(5)->format('Y-m-d'); 
            }
        }else if($period=='yearly'){
            $sdate = \Carbon\Carbon::parse($end_date)->startOfYear()->format('Y-m-d');
            $edate = \Carbon\Carbon::parse($end_date)->endOfYear()->format('Y-m-d');
            for($i=0;$i<$compare_period+1;$i++){
                $columns[$i] = ['start_date'=>$sdate, 'end_date'=>$edate, 'label'=>fdate($sdate, 'Y')];
                $sdate = \Carbon\Carbon::parse($sdate)->subYear()->format('Y-m-d'); 
                $edate = \Carbon\Carbon::parse($edate)->subYear()->format('Y-m-d'); 
            }
        }
        
        $columns = array_reverse($columns);
        $params = [
            'columns'=>$columns
        ];
        
        return $params;
    }

}
