<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Http\Resources\LedgerReportResource;
use DB;
use Auth;

class LedgerReportController extends Controller
{
    public function index(Request $request){
        $company = Auth::user()->activeCompany();
        $company_id = $company->id;

        $params = $this->getParams($request, $company_id);
        $data = $this->query($params, $company);

        $title = 'Buku Besar';
        $view = 'report.ledger.default';
        if(count($params['tags'])>0){
            $view = 'report.ledger.nobalance';
        }
        $period = ($params['start_date']==$params['end_date'])?fdate($params['start_date'], 'd M Y'):fdate($params['start_date'], 'd M Y').' s.d '.fdate($params['end_date'], 'd M Y');
        $balance_date = \Carbon\Carbon::parse($params['start_date'])->subDay()->format('d-m-Y');
        $data = array(
            'report'=>'ledger',
            'title'=>$title,
            'company'=>$company,
            'period'=>$period,
            'columns'=>$params['columns'],
            'tags'=>$request->tags,
            'accounts'=>$data[0],
            'ledgers'=>$data[1],
            'balance_date'=>$balance_date,
            'view'=>$view
        );
        // return $this->pdf($view, $data);
        if(isset($request->output)){
            $data['accounts'] = ($data['accounts'])->toArray();
            $output = $request->output;
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

        $account = DB::table("vw_accounts")
        ->where("company_id", $company->id);
        // ->where(function($query)use($start_date){
        //     $query->whereDate("trans_date", "<", $start_date)
        //     ->orWhereNull("trans_date");
        // });
        if(count($params['account_id'])){
            $account = $account->whereIn('id', $params['account_id']);
        }
        $account = $account->selectRaw("vw_accounts.id, (SELECT IF(SUM(vw_journals.total) IS NULL,0, SUM(vw_journals.total)) FROM vw_journals WHERE account_id=vw_accounts.id AND vw_journals.company_id=vw_accounts.company_id
        AND vw_journals.trans_date<'$start_date')+vw_accounts.balance AS balance")
        ->groupBy("id")
        ->pluck("balance", "id");

        // dd($account);
        // DB::enableQueryLog();
        $ledger = DB::table(DB::raw("vw_journals a"))
        ->where("a.company_id", $company->id)
        ->where("a.trans_date", ">=", $params['start_date'])
        ->where("a.trans_date", "<=", $params['end_date']);
        if(count($params['account_id'])>0){
            $ledger = $ledger->whereIn(DB::raw('a.account_id'), $params['account_id']);
        }

        if(count($params['department_id'])>0){
            $ledger = $ledger->whereIn(DB::raw('a.department_id'), $params['department_id']);
        }
        $ctags = count($params['tags']);
        if($ctags>0){
            $find = '';
            $opt = $params['tag_opt'];
            foreach($params['tags'] as $i=> $tag){
                $find.="FIND_IN_SET('$tag', tags)>0";
                if($i<$ctags-1){
                    $find.=" $opt ";
                }
            }
            $ledger = $ledger->whereRaw("($find)");
        }
        $ledger = $ledger
        ->selectRaw("a.journal_id, a.account_id, a.account_no, a.account_name,
        a.department_name, a.trans_date, a.trans_no, a.description,
        a.tags, a.debit, a.credit, a.debit_sign, a.credit_sign, a.created_by,
        (SELECT IF(SUM(b.total) IS NULL,0, SUM(b.total))
        FROM vw_journals b
        WHERE a.account_id=b.account_id AND a.company_id=b.company_id
        AND b.trans_date<'$start_date')+a.opening_balance AS opening_balance,
        (SELECT IF(SUM(c.total) IS NULL,0, SUM(c.total))
        FROM vw_journals c
        WHERE a.account_id=c.account_id AND a.company_id=c.company_id
        AND c.trans_date<='$end_date')+a.opening_balance AS final_balance,
        (SELECT IF(SUM(d.debit) IS NULL,0, SUM(d.debit))
        FROM vw_journals d
        WHERE a.account_id=d.account_id AND a.company_id=d.company_id
        AND d.trans_date>='$start_date' AND d.trans_date<='$end_date') AS total_debit,
        (SELECT IF(SUM(e.credit) IS NULL,0, SUM(e.credit))
        FROM vw_journals e
        WHERE a.account_id=e.account_id AND a.company_id=e.company_id
        AND e.trans_date>='$start_date' AND e.trans_date<='$end_date') AS total_credit
        ")
        ->orderBy(DB::raw("a.department_id, a.account_type_id, a.account_no, a.trans_date"))
        ->get();
        // dd(DB::getQueryLog());
        return [$account, $ledger];
    }
    // private function query($params, $company){
        //     $start_date = $params['start_date'];
        //     $end_date = $params['end_date'];
        //     $period = $company->getPeriod(fdate($start_date, 'Y'), fdate($start_date, 'm'));
    //     $last_period = $period[0];
    //     $start_period = $period[1];
    //     $end_period = $period[2];

    //     // DB::enableQueryLog();
    //     $account = DB::table("vw_accounts")
    //     ->where("company_id", $company->id);
    //     // ->where(function($query)use($start_date){
    //     //     $query->whereDate("trans_date", "<", $start_date)
    //     //     ->orWhereNull("trans_date");
    //     // });
    //     if(count($params['account_id'])){
    //         $account = $account->whereIn('id', $params['account_id']);
    //     }
    //     $account = $account->selectRaw("vw_accounts.id, (SELECT IF(SUM(vw_journals.total) IS NULL,0, SUM(vw_journals.total)) FROM vw_journals WHERE account_id=vw_accounts.id AND vw_journals.company_id=vw_accounts.company_id
    //     AND vw_journals.trans_date<'$start_date')+vw_accounts.balance AS balance")
    //     ->groupBy("id")
    //     ->pluck("balance", "id");

    //     // dd(DB::getQueryLog());
    //     // dd($account);
    //     $ledger = DB::table(DB::raw("vw_journals a"))
    //     ->where("a.company_id", $company->id)
    //     ->where("a.trans_date", ">=", $params['start_date'])
    //     ->where("a.trans_date", "<=", $params['end_date']);
    //     if(count($params['account_id'])>0){
    //         $ledger = $ledger->whereIn(DB::raw('a.account_id'), $params['account_id']);
    //     }

    //     if(count($params['department_id'])>0){
    //         $ledger = $ledger->whereIn(DB::raw('a.department_id'), $params['department_id']);
    //     }
    //     $ctags = count($params['tags']);
    //     if($ctags>0){
    //         $find = '';
    //         $opt = $params['tag_opt'];
    //         foreach($params['tags'] as $i=> $tag){
    //             $find.="FIND_IN_SET('$tag', tags)>0";
    //             if($i<$ctags-1){
    //                 $find.=" $opt ";
    //             }
    //         }
    //         $ledger = $ledger->whereRaw("($find)");
    //     }
    //     $ledger = $ledger
    //     ->orderBy(DB::raw("a.department_id, a.account_type_id, a.account_no, a.trans_date"))
    //     ->get();
    //     // dd(DB::getQueryLog());
    //     return [$account, $ledger];
    // }
    private function query2($params, $company){
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $period = $company->getPeriod(fdate($start_date, 'Y'), fdate($start_date, 'm'));
        $last_period = $period[0];
        $start_period = $period[1];
        $end_period = $period[2];

        if($start_date==$start_period){
            $account = DB::table("balances")
            ->where("company_id", $company->id);
            if(count($params['account_id'])){
                $account = $account->whereIn('account_id', $params['account_id']);
            }
            $account = $account->whereDate("balance_date", $last_period)
            ->pluck("balance", "account_id");
        }else{
            $account = DB::table("vw_journals")
            ->leftJoin("balances", function($join)use($last_period){
                $join->on("vw_journals.account_id","=","balances.account_id")
                ->on("vw_journals.company_id", "=", "balances.company_id")
                ->where("balance_date", "=", $last_period);
            })
            ->where("vw_journals.company_id", $company->id)
            ->where("vw_journals.trans_date", ">=",$start_period)
            ->where("vw_journals.trans_date", "<",$start_date);
            if(count($params['account_id'])){
                $account = $account->whereIn('vw_journals.account_id', $params['account_id']);
            }
            if(count($params['department_id'])){
                $account = $account->whereIn('vw_journals.department_id', $params['department_id']);
            }
            $account = $account->selectRaw("vw_journals.account_id,SUM(debit_sign+credit_sign)+IF(balance IS NULL, 0, balance) as balance")
            ->groupBy("vw_journals.account_id", "balance")
            ->pluck("balance", "account_id");
        }
        // DB::enableQueryLog();
        $ledger = DB::table(DB::raw("vw_journals a"))
        ->where("a.company_id", $company->id)
        ->where("a.trans_date", ">=", $params['start_date']);
        // ->where("a.trans_date", "<=", $params['end_date']);
        if(count($params['account_id'])>0){
            $ledger = $ledger->whereIn(DB::raw('a.account_id'), $params['account_id']);
        }

        if(count($params['department_id'])>0){
            $ledger = $ledger->whereIn(DB::raw('a.department_id'), $params['department_id']);
        }
        $ctags = count($params['tags']);
        if($ctags>0){
            $find = '';
            $opt = $params['tag_opt'];
            foreach($params['tags'] as $i=> $tag){
                $find.="FIND_IN_SET('$tag', tags)>0";
                if($i<$ctags-1){
                    $find.=" $opt ";
                }
            }
            $ledger = $ledger->whereRaw("($find)");
        }
        $ledger = $ledger
        ->orderBy(DB::raw("a.department_id, a.account_type_id, a.account_no, a.trans_date"))
        ->get();
        // dd(DB::getQueryLog());
        return [$account->toArray(),$ledger];
    }

    private function getParams(Request $request, $company_id){
        $accounts = $request->accounts??[];
        $tags = $request->tags??[];
        $tag_opt = $request->query('tag_opt', 'or');
        $departments = $request->departments??[];
        $columns = ['tags'=>false, 'created_by'=>false, 'department'=>false];


        $start_date = $request->start_date;
        $end_date = $request->end_date;
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
            'account_id'=>$accounts,
            'tags'=>$tags,
            'tag_opt'=>$tag_opt,
            'department_id'=>$departments,
            'start_date'=>$start_date,
            'end_date'=>$end_date
        ];
        return $params;
    }

}
