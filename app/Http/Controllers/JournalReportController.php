<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\JournalReportResource;
use App\Company;
use DB;
use Auth;

class JournalReportController extends Controller
{
    public function voucher(Request $request){
        $company_id = Auth::user()->activeCompany()->id;
        return $this->index($request, $company_id, 1);
    }
    public function journal(Request $request){
        $company_id = Auth::user()->activeCompany()->id;
        return $this->index($request, $company_id);
    }
    public function index(Request $request, $company_id, $is_voucher=0){
        $company = Company::findOrFail(decode($company_id));
        $company_id = $company->id;
        $params = $this->getParams($request, $company_id, $is_voucher);
        $data = $this->query($params, $company, $is_voucher);
        $title = 'Laporan '.($is_voucher?'Voucher':'Jurnal');
        $view = 'report.journal.'.($params['layout']=='detail'?'default':'summary');
        $period = ($params['start_date']==$params['end_date'])?fdate($params['start_date'], 'd M Y'):fdate($params['start_date'], 'd M Y').' s.d '.fdate($params['end_date'], 'd M Y');
        $data = array(
            'report'=>$is_voucher?'voucher':'journal',
            'title'=>$title,
            'company'=>$company,
            'period'=>$period,
            'columns'=>$params['columns'],
            'journals'=>$data,
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
        // return view('report.viewer', $data);
        return view('report.pdf', $data);
    }
    private function print($data){
        return view('report.print',$data);
    }
    private function pdf($view, $data){
        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('report.pdf', $data);
        return $pdf->stream('journal.pdf');
    }

    private function query($params, $company, $is_voucher){
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $period = $company->getPeriod(fdate($start_date, 'Y'), fdate($start_date, 'm'));
        $last_period = $period[0];
        $start_period = $period[1];
        $end_period = $period[2];
        $table = $is_voucher==1?'vw_voucher':'vw_journals';

        // DB::enableQueryLog();
        $journal = DB::table($table)
        ->where("company_id", $company->id)
        ->where("trans_date", ">=", $params['start_date'])
        ->where("trans_date", "<=", $params['end_date']);
        if(count($params['journal_id'])>0){
            $journal = $journal->whereIn('journal_id', $params['journal_id']);
        }
        if(!empty($params['trans_group'])){
            $journal = $journal->where('numbering_id', $params['trans_group']);
        }

        if(count($params['department_id'])>0){
            $journal = $journal->whereIn('department_id', $params['department_id']);
        }
        $journal = $journal
        ->select(['journal_id', 'department_custom_id','department_name', 'department_id', 'trans_date', 'trans_no', 'sequence','description', 'created_at', 'debit',
        'credit', 'total', 'account_id','account_no', 'account_name', 'tags','journal_description', 'created_by', 'balance'
        ]);
        $sort_key = $params['sort_key'];
        $sort_order = $params['sort_order'];
        if(empty($params['sort_key'])){
            $journal = $journal->orderBy('trans_date');
            $journal = $journal->orderBy('trans_no');
            $journal = $journal->orderBy('journal_detail_id');
            $journal = $journal->orderBy('sequence');
        }else{
            $journal = $journal->orderBy($sort_key, $sort_order);
            $journal = $journal->orderBy("trans_date", $sort_order);
            $journal = $journal->orderBy('trans_no');
            $journal = $journal->orderBy('journal_detail_id');
            $journal = $journal->orderBy('sequence');
        }
        $journal = $journal->get();
        // dd(DB::getQueryLog());
        return $journal;
    }

    private function getParams(Request $request, $company_id, $is_voucher=0){
        $layout = $request->query('layout', 'detail');
        $journals = $request->journals??[];
        if(!empty($request->id)){
            $journals = $request->id??[];
            if(!is_array($journals)){
                $journals = [$journals];
            }
        }
        $departments = $request->departments??[];
        $opt_columns = $request->columns;
        $sort_key = $request->sort_key;
        $sort_order = $request->sort_order??'asc';
        // dd($department_id);

        $columns = ['tags'=>empty($request->tags)?false:true,
        'description'=>empty($request->description)?false:true,
        'created_by'=>empty($request->created_by)?false:true,
        'department'=>empty($request->department)?false:true,];

        $paramsString = "layout=$layout";

        $period = $request->period;
        $start_date = fdate($request->start_date, 'Y-m-d');
        $end_date = fdate($request->end_date, 'Y-m-d');
        $table = $is_voucher==1?'vw_voucher':'vw_journals';
        if(empty($start_date)  && empty($end_date)){
            $max_date = DB::table($table)
            ->where('company_id', $company_id)->max('trans_date');
            if($max_date==null){
                $max_date = date('Y-m-d');
            }
            $start_date = \Carbon\Carbon::parse($max_date)->startOfMonth()->format('Y-m-d');
            $end_date = $max_date;
        }

        if(count($journals)>0){
            $end_date = DB::table($table)
            ->where('company_id', $company_id)
            ->whereIn('journal_id', $journals)
            ->max('trans_date');
            $start_date = DB::table($table)
            ->where('company_id', $company_id)
            ->whereIn('journal_id', $journals)
            ->min('trans_date');
        }

        $start_date = fdate($start_date, 'Y-m-d');
        $end_date = fdate($end_date, 'Y-m-d');

        $params = [
            'layout'=>$layout,
            'columns'=>$columns,
            'trans_group'=>$request->trans_group,
            'journal_id'=>$journals??[],
            'department_id'=>$departments??[],
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'sort_key'=>$sort_key,
            'sort_order'=>$sort_order,
        ];
        return $params;
    }
}
