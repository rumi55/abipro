<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use DB;

class IncomeReportController extends Controller
{
    public function index(Request $request, $company_id){
        $company = Company::findOrFail(decode($company_id));
        $company_id = $company->id;
        $params = $this->getParams($request, $company_id);
        
        $results = $this->query($params, $company);
        $title = 'Laporan Laba-Rugi';
        $view = 'report.income.default';
        $period = ($params['start_date']==$params['end_date'])?fdate($params['start_date'], 'd M Y'):fdate($params['start_date'], 'd M Y').' s.d '.fdate($params['end_date'], 'd M Y');
        $balance_date = \Carbon\Carbon::parse($params['start_date'])->subDay()->format('d-m-Y');
        
        $data = array(
            'title'=>$title,
            'company'=>$company,
            'period'=>$params['period'],
            'income'=>$results[0],
            'return'=>$results[1],
            'discount'=>$results[2],
            'total_income'=>$results[3],
            'other_income'=>$results[4],
            'total_other_income'=>$results[5],
            'cogs'=>$results[6],
            'total_cogs'=>$results[7],
            'expense'=>$results[8],
            'total_expense'=>$results[9],
            'other_expense'=>$results[10],
            'total_other_expense'=>$results[11],
        );
        if(isset($request->output)){
            $output = $request->output;
            if($output=='pdf'){
                return $this->pdf($view, $data);
            }else if($output=='excel'){
                return $this->html($view, $data);
            }else if($output=='csv'){
                return $this->html($view, $data);
            }else if($output=='html'){
                return $this->html($view, $data);
            }
        }
        return $this->json($data, $params);
    }
    private function json($data, $params){
        $json = [
            'params'=>$params['params'],
            'data'=>$data['accounts']
        ];
        return response()->json($json);
    }
    private function html($view, $data){
        return view($view, $data);
    }
    private function pdf($view, $data){
        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView($view, $data);
        return $pdf->stream('balance.pdf');
    }
    
    private function query($params, $company){
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $cuttoff_date= $params['cutoff_date'];
        
        $select_income = [
            DB::raw('a.account_type, a.account_group, a.account_no, a.account_name')
        ];
        $select_total_income = [
            DB::raw('a.type')
        ];
        $select_return = [
            DB::raw('a.account_type, a.account_group, a.account_no, a.account_name')
        ];
        $select_total_return = [
            DB::raw('a.type')
        ];
        $select_discount = [
            DB::raw('a.account_type, a.account_group, a.account_no, a.account_name')
        ];
        $select_total_discount = [
            DB::raw('a.type')
        ];
        $select_other_income = [
            DB::raw('a.account_type, a.account_group, a.account_no, a.account_name')
        ];
        $select_total_other_income = [
            DB::raw('a.type')
        ];
        $select_total_cogs = [
            DB::raw('a.type')
        ];
        $select_total_expense = [
            DB::raw('a.type')
        ];
        $select_total_other_expense = [
            DB::raw('a.type')
        ];
        foreach($params['period'] as $i=>$p){
            $select_income[] = DB::raw ("(SELECT IF(SUM(c$i.total) IS NULL, 0,SUM(c$i.total))
            FROM vw_ledger c$i WHERE c$i.`trans_date`<='$p' AND  c$i.company_id=a.company_id 
            and a.id=c$i.account_id) as total_$i");
            $select_total_income[] = DB::raw ("(SELECT IF(SUM(c$i.total) IS NULL, 0,SUM(c$i.total))
            FROM vw_ledger c$i WHERE c$i.`trans_date`<='$p' AND  c$i.company_id=a.company_id 
            and c$i.account_type_id=12) as total_$i");
            $select_other_income[] = DB::raw ("(SELECT IF(SUM(c$i.total) IS NULL, 0,SUM(c$i.total))
            FROM vw_ledger c$i WHERE c$i.`trans_date`<='$p' AND  c$i.company_id=a.company_id 
            and a.id=c$i.account_id) as total_$i");
            $select_total_other_income[] = DB::raw ("(SELECT IF(SUM(c$i.total) IS NULL, 0,SUM(c$i.total))
            FROM vw_ledger c$i WHERE c$i.`trans_date`<='$p' AND  c$i.company_id=a.company_id 
            and c$i.account_type_id=13) as total_$i");
            $select_total_cogs[] = DB::raw ("(SELECT IF(SUM(c$i.total) IS NULL, 0,SUM(c$i.total))
            FROM vw_ledger c$i WHERE c$i.`trans_date`<='$p' AND  c$i.company_id=a.company_id 
            and c$i.account_type_id=14) as total_$i");
            $select_total_expense[] = DB::raw ("(SELECT IF(SUM(c$i.total) IS NULL, 0,SUM(c$i.total))
            FROM vw_ledger c$i WHERE c$i.`trans_date`<='$p' AND  c$i.company_id=a.company_id 
            and c$i.account_type_id=15) as total_$i");
            $select_total_other_expense[] = DB::raw ("(SELECT IF(SUM(c$i.total) IS NULL, 0,SUM(c$i.total))
            FROM vw_ledger c$i WHERE c$i.`trans_date`<='$p' AND  c$i.company_id=a.company_id 
            and c$i.account_type_id=16) as total_$i");
        }
        // DB::enableQueryLog();
        //income
        $income = DB::table(DB::raw('vw_accounts a'))
        ->select($select_income)
        ->whereRaw("a.company_id='$company->id' AND a.has_children=false")
        ->whereIn('a.account_type_id',[12])
        ->orderBy(DB::raw('a.id, a.account_no'))->get();
        
        //query total income
        $total_income = DB::table(DB::raw('vw_accounts a'))
        ->select($select_total_income)
        ->whereRaw("a.company_id='$company->id'")
        ->whereIn('a.account_type_id',[12])
        // ->whereIn('a.type',['REV', 'RTN', 'DSC'])
        ->orderBy(DB::raw('a.id, a.account_no'))->first();

        //return penjualan
        $return = DB::table(DB::raw('vw_accounts a'))
        ->select($select_income)
        ->whereRaw("a.company_id='$company->id' AND a.has_children=false")
        ->whereIn('a.type',['RTN'])
        ->orderBy(DB::raw('a.id, a.account_no'))->get();
        
        //discount penjualan
        $discount = DB::table(DB::raw('vw_accounts a'))
        ->select($select_income)
        ->whereRaw("a.company_id='$company->id' AND a.has_children=false")
        ->whereIn('a.type',['RTN'])
        ->orderBy(DB::raw('a.id, a.account_no'))->get();

        $other_income = DB::table(DB::raw('vw_accounts a'))
        ->select($select_income)
        ->whereRaw("a.company_id='$company->id' AND a.has_children=false")
        ->whereIn('a.account_type_id',[13])
        ->orderBy(DB::raw('a.id, a.account_no'))->get();
        
        //query total income
        $total_other_income = DB::table(DB::raw('vw_accounts a'))
        ->select($select_total_other_income)
        ->whereRaw("a.company_id='$company->id'")
        ->whereIn('a.account_type_id',[13])
        ->orderBy(DB::raw('a.id, a.account_no'))->first();
        //cogs
        $cogs = DB::table(DB::raw('vw_accounts a'))
        ->select($select_income)
        ->whereRaw("a.company_id='$company->id' AND a.has_children=false")
        ->whereIn('a.account_type_id',[14])
        ->orderBy(DB::raw('a.id, a.account_no'))->get();
        
        //query total cogs
        $total_cogs = DB::table(DB::raw('vw_accounts a'))
        ->select($select_total_cogs)
        ->whereRaw("a.company_id='$company->id'")
        ->whereIn('a.account_type_id',[14])
        ->orderBy(DB::raw('a.id, a.account_no'))->first();
        //expense
        $expense = DB::table(DB::raw('vw_accounts a'))
        ->select($select_income)
        ->whereRaw("a.company_id='$company->id' AND a.has_children=false")
        ->whereIn('a.account_type_id',[15])
        ->orderBy(DB::raw('a.id, a.account_no'))->get();
        
        //query total expense
        $total_expense = DB::table(DB::raw('vw_accounts a'))
        ->select($select_total_expense)
        ->whereRaw("a.company_id='$company->id'")
        ->whereIn('a.account_type_id',[15])
        ->orderBy(DB::raw('a.id, a.account_no'))->first();
        
        //other expense
        $other_expense = DB::table(DB::raw('vw_accounts a'))
        ->select($select_income)
        ->whereRaw("a.company_id='$company->id' AND a.has_children=false")
        ->whereIn('a.account_type_id',[16])
        ->orderBy(DB::raw('a.id, a.account_no'))->get();
        
        //query total other expense
        $total_other_expense = DB::table(DB::raw('vw_accounts a'))
        ->select($select_total_other_expense)
        ->whereRaw("a.company_id='$company->id'")
        ->whereIn('a.account_type_id',[16])
        ->orderBy(DB::raw('a.id, a.account_no'))->first();

        // DB::enableQueryLog();
        // dd(DB::getQueryLog());
        return [
            $income, $return, $discount, 
            $total_income, 
            $other_income, $total_other_income,
            $cogs, $total_cogs,
            $expense, $total_expense, $other_expense, $total_other_expense
        ];
    }

    private function getParams(Request $request, $company_id){
        $department_id = $request->departments;
        $departments = [];
        if(!empty($department_id)){
            $exploded = explode(',',$department_id);
            foreach($exploded as $id){
                // $departments[] = decode($id);
                $departments[] = intval($id);
            }
        }
        
        $period = $request->period;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $compare = $request->compare;
        $period = $request->period;
        $p = array();
        $date = \Carbon\Carbon::parse($start_date);
        for($i=0;$i<$period+1;$i++){
            if($i==0){
                $p[$i] = $start_date;
            }else{
                if($compare=='weekly'){
                    $p[$i] = $date->subWeek()->format('Y-m-d');
                }else if($compare=='monthly'){
                    if($i==1){
                        $sdate=$date->startOfMonth();
                    }else{
                        $sdate=$sdate->startOfMonth();
                    }
                    $p[$i] = $sdate->subMonth()->endOfMonth()->format('Y-m-d');
                }else if($compare=='quarterly'){
                    if($i==1){
                        $sdate=$date->startOfMonth();
                    }else{
                        $sdate=$sdate->startOfMonth();
                    }
                    $p[$i] = $sdate->subQuarter()->endOfMonth()->format('Y-m-d');
                }else if($compare=='semi-yearly'){
                    if($i==1){
                        $sdate=$date->startOfMonth();
                    }else{
                        $sdate=$sdate->startOfMonth();
                    }
                    $p[$i] = $sdate->subMonths(6)->endOfMonth()->format('Y-m-d');
                }else if($compare=='yearly'){
                    if($i==1){
                        $sdate=$date->endOfYear();
                    }
                    $p[$i] = $sdate->subYear()->format('Y-m-d');
                }
            }
        }
        $cutoff_date = date('Y-m-d');//tanggal tutup buku
        
        $params = [
            'params'=>[
                'start_date'=>$start_date,
                'compare'=>$compare,
                'period'=>$period,
                'departments'=>$department_id,
            ],
            'department_id'=>$departments,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'cutoff_date'=>$cutoff_date,
            'period'=>$p
        ];
        return $params;
    }
}
