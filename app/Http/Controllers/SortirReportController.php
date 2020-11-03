<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Http\Resources\LedgerReportResource;
use DB;
use Auth;

class SortirReportController extends Controller
{
    public function index(Request $request){
        $company = Auth::user()->activeCompany();
        $company_id = $company->id;

        $layout = $request->query('layout', 'detail');
        $title = 'Laporan Sortir';
        $view = 'report.sortir.default';

        $params = $this->getParams($request, $company_id);

        $period = ($params['start_date']==$params['end_date'])?fdate($params['start_date'], 'd M Y'):fdate($params['start_date'], 'd M Y').' s.d '.fdate($params['end_date'], 'd M Y');
        $balance_date = \Carbon\Carbon::parse($params['start_date'])->subDay()->format('d-m-Y');
        $data = array(
            'report'=>'sortir',
            'title'=>$title,
            'company'=>$company,
            'period'=>$period,
            'params'=>$params,
            'columns'=>$params['columns'],
            'view'=>$view
        );
        if($layout=='summ'){
            $data['accounts'] = $this->querySumm($params, $company);
            $data['view'] = 'report.sortir.summ';
        }else{
            $data['ledgers'] = $this->query($params, $company);
        }

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
        $pdf->loadView('report.pdf', $data);
        return $pdf->download($data['report'].'.pdf');
    }

    private function query($params, $company){
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $period = $company->getPeriod(fdate($start_date, 'Y'), fdate($start_date, 'm'));
        $last_period = $period[0];
        $start_period = $period[1];
        $end_period = $period[2];

        $ledger = DB::table(DB::raw("vw_journals a"))
        ->leftJoin(DB::raw('tags b'), DB::raw('FIND_IN_SET(b.id, tags)'), '>', DB::raw('0'))
        ->where("a.company_id", $company->id)
        ->whereNotNull('tags')
        ->where("a.trans_date", ">=", $params['start_date'])
        ->where("a.trans_date", "<=", $params['end_date']);
        if(count($params['account_id'])>0){
            $ledger = $ledger->whereIn(DB::raw('a.account_id'), $params['account_id']);
        }

        if(count($params['department_id'])>0){
            $ledger = $ledger->whereIn(DB::raw('a.department_id'), $params['department_id']);
        }
        if(!empty($params['sortir'])){
            $ledger = $ledger->whereRaw("b.`group`='".$params['sortir']."'");
        }
        if(!empty($params['sortir_items'])){
            $ledger = $ledger->whereIn("b.id", $params['sortir_items']);
        }
        $ledger = $ledger
        ->selectRaw("a.journal_id, a.account_id, a.account_no, a.account_name,
        a.department_name, a.trans_date, a.trans_no, a.description,
        a.tags, a.debit, a.credit, a.debit_sign, a.credit_sign, a.created_by,
        b.id as tag_id, b.group as tag_group, b.item_id, b.item_name")
        ->orderBy(DB::raw("b.`group`, b.item_id, a.department_id, a.account_type_id, a.account_no, a.trans_date"))
        ->get();

        return $ledger;
    }
    private function querySumm($params, $company){
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $period = $company->getPeriod(fdate($start_date, 'Y'), fdate($start_date, 'm'));
        $last_period = $period[0];
        $start_period = $period[1];
        $end_period = $period[2];
        $sortirs = [];

        if(!empty($params['sortir_items'])){
            $sortirs = $params['sortir_items'];
        }else{
            $sortirs = DB::table('tags')->where('group', $params['sortir'])->pluck('id')->toArray();
        }

        $csortirs = count($sortirs);

        $find_sortird = '';
        $find_sortire = '';
        $find_sortirf = '';
        $find_sortirg = '';
        if($csortirs>0){
            $opt = ' OR ';
            foreach($sortirs as $i=> $tag){
                $find_sortird.="FIND_IN_SET('$tag', d.tags)>0";
                $find_sortire.="FIND_IN_SET('$tag', e.tags)>0";
                $find_sortirf.="FIND_IN_SET('$tag', f.tags)>0";
                $find_sortirg.="FIND_IN_SET('$tag', g.tags)>0";
                if($i<$csortirs-1){
                    $find_sortird.=" $opt ";
                    $find_sortire.=" $opt ";
                    $find_sortirf.=" $opt ";
                    $find_sortirg.=" $opt ";
                }
            }
            $find_sortird=" AND ($find_sortird) ";
            $find_sortire=" AND ($find_sortire) ";
            $find_sortirf=" AND ($find_sortirf) ";
            $find_sortirg=" AND ($find_sortirg) ";
        }
        // DB::enableQueryLog();

        $balance = DB::table(DB::raw('vw_accounts a'))
        ->select([
            DB::raw('a.id, a.account_no, a.account_name'),
            DB::raw("(SELECT IF(SUM(d.debit) IS NULL,0,SUM(d.debit)) FROM vw_journals d
            WHERE d.account_id=a.id AND a.company_id=d.company_id
            AND d.trans_date>='$start_date' AND d.trans_date<='$end_date' $find_sortird
            ) as debit"),
            DB::raw("(SELECT IF(SUM(e.credit) IS NULL, 0, SUM(e.credit)) FROM vw_journals e
            WHERE e.account_id=a.id AND a.company_id=e.company_id
            AND e.trans_date>='$start_date' AND e.trans_date<='$end_date' $find_sortire
            ) as credit"),
            DB::raw("(SELECT IF(SUM(f.total) IS NULL, 0, SUM(f.total))+a.balance FROM vw_journals f
            WHERE f.account_id=a.id AND a.company_id=f.company_id
            AND f.trans_date<'$start_date' $find_sortirf
            ) as op_balance"),
            DB::raw("(SELECT IF(SUM(g.total) IS NULL, 0, SUM(g.total))+a.balance FROM vw_journals g
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

    private function getParams(Request $request, $company_id){
        $start_date = $request->start_date??date('Y-m-d');
        $end_date = $request->end_date??date('Y-m-d');
        $sortir = $request->sortir;
        $sortir_items = $request->sortir_items;

        $accounts = $request->accounts??[];
        $departments = $request->departments??[];

        $columns = ['tags'=>false, 'created_by'=>false, 'department'=>false];


        if(empty($start_date)){
            $max_date = DB::table('vw_journals')
            ->where('company_id', $company_id)->max('trans_date');
            if($max_date==null){
                $max_date = date('Y-m-d');
            }
            $start_date = $max_date;
            $end_date = $max_date;
        }

        $columns = ['tags'=>empty($request->tags)?false:true,
        'description'=>empty($request->description)?false:true,
        'created_by'=>empty($request->created_by)?false:true,
        'department'=>empty($request->department)?false:true,];

        $start_date = fdate($start_date, 'Y-m-d');
        $end_date = fdate($end_date, 'Y-m-d');

        $params = [
            'columns'=>$columns,
            'sortir'=>$sortir,
            'sortir_items'=>$sortir_items,
            'account_id'=>$accounts,
            'department_id'=>$departments,
            'start_date'=>$start_date,
            'end_date'=>$end_date
        ];
        return $params;
    }

}
