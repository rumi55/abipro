<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ClosingBookResource;
use App\ClosingBook;
use App\Journal;
use Auth;
use DB;

class ClosingBookController extends Controller
{
    public function getAll(Request $request){
        $company_id = Auth::user()->activeCompany()->id;
        $page_size = $request->query('page_size', 10);
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $book = ClosingBook::where('company_id', $company_id);
        
        $sort = $request->query('sort');
        if(!empty($sort)){
            $sort = explode('-',$sort);
            $sort_key=$sort[0];
            $sort_order=count($sort)==2?(substr($sort[1], 0, 3)=='asc'?'asc':'desc'):'asc';
            $book = $book->orderBy($sort_key, $sort_order);
        }
        
        if(!empty($sort_key)){
            $book = $book->orderBy($sort_key, $sort_order);
        }        
        $book = $book->paginate($page_size)->appends($request->query());
        return ClosingBookResource::collection($book);
    }
    public function get(Request $request, $id){
        $id = decode($id);
        $book = ClosingBook::findOrFail($id);
        return new ClosingBookResource($book);
    }
    public function check(Request $request, $date){
        $book = ClosingBook::where('closing_date', '<=', $date)->orderBy('closing_date', 'desc')->first();
        return new ClosingBookResource($book);
    }
    public function create(Request $request){
        validate($request->all(), [
            'start_date' => 'required',
            'end_date' => 'required',
            'account_id' => 'required',
            'profit' => 'required',
            'notes' => 'max:256',
            'status' => 'max:32',
        ]);
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $debit = $request->profit<0?$request->profit:0;
        $credit = $request->profit>0?$request->profit:0;
        $book = ClosingBook::create([
            'start_date'=>fdate($request->start_date, 'Y-m-d'),
            'end_date'=>fdate($request->end_date, 'Y-m-d'),
            'account_id'=>decode($request->account_id),
            'profit'=>$request->profit,
            'debit'=>$debit,
            'credit'=>$credit,
            'notes'=>$request->notes,
            'status'=>$request->status,
            'created_by'=>$user->id,
            'company_id'=>$company_id,
        ]);
        return (new ClosingBookResource($book))
                ->response()
                ->setStatusCode(201);
    }
    public function financialSummary(Request $request){
        $company = Auth::user()->activeCompany();
        
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if($start_date>$end_date){
            $errors =[''];
            throw new \App\Exceptions\ApiValidationException($errors);
        }
        $balance = DB::table('vw_ledger')
        ->selectRaw('account_type_id, account_type_name, IF(SUM(total) IS NULL, 0, SUM(total)) as total')
        ->whereRaw("company_id=$company->id AND (type IS NULL OR type<>'tax') AND (trans_date>='$start_date' AND trans_date<='$end_date')")
        ->whereIn('account_type_id', [12, 13, 14, 15, 16])
        ->orderBy('account_type_id')
        ->groupBy('account_type_id', 'account_type_name')
        ->get();
        $tax = DB::table('vw_ledger')
        ->selectRaw('IF(SUM(total) IS NULL, 0, SUM(total)) as total')
        ->whereRaw("company_id=$company->id AND type='tax' AND (trans_date>='$start_date' AND trans_date<='$end_date')")
        ->value('total');

        $income = 0;
        $cogs = 0;
        $expense = 0;
        $other_income = 0;
        $other_expense = 0;
        foreach($balance as $b){
            $income =$b->account_type_id==12?$b->total:$income;
            $other_income =$b->account_type_id==13?$b->total:$other_income;
            $cogs =$b->account_type_id==14?$b->total:$cogs;
            $expense =$b->account_type_id==15?$b->total:$expense;
            $other_expense =$b->account_type_id==16?$b->total:$other_expense;
        }
        $gross_profit = $income-$cogs;
        $ops_profit = $gross_profit-$expense;
        $net_profit = $ops_profit+$other_income-$other_expense;
        $net_profit_tax = $net_profit-$tax;
        return response()->json(
            [
                'meta'=>[
                    'start_date'=>$start_date,
                    'end_date'=>$end_date
                ],
                'data'=>[
                    'income'=>$income,
                    'cogs'=>$cogs,
                    'gross_profit'=>$gross_profit,
                    'expense'=>$expense,
                    'ops_profit'=>$ops_profit,
                    'other_income'=>$other_income,
                    'other_expense'=>$other_expense,
                    'net_profit'=>$net_profit,
                    'tax'=>$tax,
                    'net_profit_tax'=>$net_profit,
                ]
            ]
        );
    }
    public function update(Request $request, $id){
        
    }
    public function cancel(Request $request, $id){
        $model = ClosingBook::findOrFail(decode($id));
        $model->delete();
        return response()->json(null, 204);
    }

    public function getStartPeriod(){
        $company = Auth::user()->activeCompany();
        $company_id = $company->id;
        $start_date = ClosingBook::where('company_id', $company_id)->max('end_date');//->orderBy('closing_date', 'desc')->first();        
        if($start_date==null){
            $start_date = Journal::where('company_id', $company_id)->min('trans_date');
            $start_date = \Carbon\Carbon::parse($start_date)->startOfMonth()->format('Y-m-d');
        }else{
            $start_date = \Carbon\Carbon::parse($start_date)->endOfMonth()->addDay(1)->format('Y-m-d');
        }
        $data['data'] = ['start_date'=>$start_date];
        return response()->json($data);
    }
}
