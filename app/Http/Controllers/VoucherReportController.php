<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use DB;

class VoucherReportController extends Controller
{
    public function index(Request $request, $company_id){
        $company = Company::findOrFail(decode($company_id));
        $company_id = $company->id;
        $params = $this->getParams($request, $company_id);
        $data = $this->query($params, $company);
        $title = 'Voucher';
        $view = 'report.journal.default';
        // $view = 'report.journal.summary';
        $period = ($params['start_date']==$params['end_date'])?fdate($params['start_date'], 'd M Y'):fdate($params['start_date'], 'd M Y').' s.d '.fdate($params['end_date'], 'd M Y');
        $data = array(
            'title'=>$title,
            'company'=>$company,
            'period'=>$period,
            'journals'=>$data,
            'view'=>$view
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
            'start_date'=>$params['start_date'],
            'end_date'=>$params['end_date'],
            'data'=>$data['journals']
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
        return $pdf->stream('journal.pdf');
    }
    
    private function query($params, $company){
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $period = $company->getPeriod(fdate($start_date, 'Y'), fdate($start_date, 'm'));
        $last_period = $period[0];
        $start_period = $period[1];
        $end_period = $period[2];
        
        // DB::enableQueryLog();
        $journal = DB::table(DB::raw("vw_voucher a"))
        ->where("a.company_id", $company->id)
        ->where("a.trans_date", ">=", $params['start_date'])
        ->where("a.trans_date", "<=", $params['end_date']);
        if(count($params['journal_id'])>0){
            $journal = $journal->whereIn(DB::raw('a.journal_id'), $params['journal_id']);
        }

        if(count($params['department_id'])>0){
            $journal = $journal->whereIn(DB::raw('a.department_id'), $params['department_id']);
        }
        $journal = $journal
        ->select(['journal_id', 'department_name', 'trans_date', 'trans_no', 'sequence','description', 'created_at', 'debit',
        'credit', 'total', 'account_no', 'account_name']);
        $sort_order = $params['sort_order'];
        $sort_key = $params['sort_key'];
        if(empty($params['sort_key'])){
            $journal = $journal->orderBy("trans_date");
            $journal = $journal->orderBy("trans_no");
        }else{
            $journal = $journal->orderBy($sort_key, $sort_order);
            $journal = $journal->orderBy("trans_date", $sort_order);
        }
        $journal = $journal->get();
        // dd(DB::getQueryLog());
        return $journal;
    }

    private function getParams(Request $request, $company_id){
        $journal_id = $request->journals;
        $department_id = $request->departments;
        $sort_key = $request->sort_key;
        $sort_order = empty($request->sort_order)?'asc':$request->sort_order;
        // dd($department_id);
        $journals = [];
        $departments = [];
        $paramsString = '';
        if(!empty($journal_id)){
            $exploded = explode(',',$journal_id);
            $paramsString.="journals=$journal_id";
            $journal_id = [];
            foreach($exploded as $id){
                // $journals[] = $id;
                $journals[] = decode($id);
            }
        }
        
        if(!empty($department_id)){
            $exploded = explode(',',$department_id);
            $paramsString.=($paramsString!=''?'&':'')."departments=$department_id";
            foreach($exploded as $id){
                $departments[] = decode($id);
                // $departments[] = intval($id);
            }
        }
        
        $period = $request->period;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if(empty($start_date)  && empty($end_date)){
            $max_date = DB::table('vw_voucher')
            ->where('company_id', $company_id)->max('trans_date');
            if($max_date==null){
                $max_date = date('Y-m-d');
            }
            $start_date = \Carbon\Carbon::parse($max_date)->startOfMonth()->format('Y-m-d');
            $end_date = $max_date;
        }
        
        if(count($journals)>0){
            $end_date = DB::table('vw_voucher')
            ->where('company_id', $company_id)
            ->whereIn('journal_id', $journals)
            ->max('trans_date');
            $start_date = DB::table('vw_voucher')
            ->where('company_id', $company_id)
            ->whereIn('journal_id', $journals)
            ->min('trans_date');
        }
        

        $start_date = fdate($start_date, 'Y-m-d');
        $end_date = fdate($end_date, 'Y-m-d');
        $paramsString.=($paramsString!=''?'&':'')."start_date=$start_date&end_date=$end_date";
        if(!empty($sort_key)){
            $paramsString.=($paramsString!=''?'&':'')."sort_key=$sort_key&sort_order=$sort_order";
        }
        
        $params = [
            'params'=>$paramsString,
            'journal_id'=>$journals,
            'department_id'=>$departments,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'sort_key'=>$sort_key,
            'sort_order'=>$sort_order,
        ];
        
        return $params;
    }
}
