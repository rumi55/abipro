<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\BudgetResource;
use App\Http\Resources\AccountResource;
use App\Exceptions\ApiValidationException;
use App\Budget;
use App\Account;
use Auth;
use Validator;
use PDF;
use DB;

class BudgetController extends Controller
{
    public function index(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();
        $company_id = $company->id;
        $start_month = $request->query('start_month', date('Y-m'));
        $end_month = $request->query('end_month', date('Y-m'));
        
        $start = \Carbon\Carbon::parse($company->accounting_start_date)->startOfYear();
        $end = \Carbon\Carbon::now()->endOfYear();
        $current_month = $start;
        $month_names = [];
        while($current_month<=$end){
            $month_names[] = array('value'=>$current_month->format('Y-m'), 'text'=>$current_month->format('M Y'));   
            $current_month = $current_month->addMonth();  
        }
        $smonth = \Carbon\Carbon::parse($start_month);
        $emonth = \Carbon\Carbon::parse($end_month);
        $list_month = array();
        $columns = array();
        $current_month = $smonth;
        
        $select = ['vw_accounts.id', 'account_no','account_name', 'account_type'];
        while($current_month<=$emonth){
            $month = $current_month->format('Y-m');
            $col = $current_month->format('Y-m');
            $select[] = DB::raw("(SELECT budget FROM budgets where account_id=vw_accounts.id AND company_id=$company_id AND budget_month='$month') as ".$col);
            $list_month[] = $current_month->format('M Y');   
            $months[] = $current_month->format('Y-m');   
            $columns[] = $current_month->format('Y-m');   
            $current_month = $current_month->addMonth();  
        }
        $accounts = Account::where('company_id', $company_id)->where('has_children', 0);
        $accounts = $accounts->whereHas('accountType', function ($q){
            $q->whereIn('group', ['expense',  'income']);
        })->get();
        
        
        $start_month = $months[0];
        $end_month = $months[count($months)-1];

        $budgets = Budget::where('budget_month', '>=', $start_month)->where('budget_month','<=', $end_month)
        ->selectRaw("concat(account_id, '_', budget_month) as id, budget")->pluck('budget', 'id')->toArray();
        
        $data =[ 'months'=>$list_month, 'start_month'=>$start_month, 'end_month'=>$end_month, 'month_list'=>$month_names, 'columns'=>$columns, 'budgets'=>$budgets, 'accounts'=>$accounts];
        
        return view('account.budget', $data);
    }
    public function get($id){
        $id = decode($id);
        return new BudgetResource(Budget::findOrFail($id));
    }
    
    public function save(Request $request){
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $data = $request->all();
        
        foreach($data as $key => $dt){
            $k = explode('_', $key);
            if(count($k)==3){
                $dt = $dt ?? 0;
                Budget::updateOrCreate(
                    [
                    'budget_month'=>$k[2],
                    'company_id'=>$company_id,
                    'account_id'=>$k[1],
                    'department_id'=>null
                    ],
                    [
                        'budget_month'=>$k[2],
                        'company_id'=>$company_id,
                        'account_id'=>$k[1],
                        'department_id'=>null,
                        'budget'=>parse_number($dt)
                    ]
                );
            }
        }
        return redirect()->route('accounts.budgets')->with('success', 'Anggaran telah disimpan.');
    }
    public function create(Request $request){
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $accounts = $request->data;
        $department = null;
        foreach($accounts as $account){
            $budgets = $account['budgets'];
            foreach($budgets as $b){
                Budget::updateOrCreate(
                    [
                    'budget_month'=>$b['budget_month'],
                    'company_id'=>$company_id,
                    'account_id'=>$account['account_id'],
                    'department_id'=>$department
                    ],
                    [
                    'budget_month'=>$b['budget_month'],
                    'company_id'=>$company_id,
                    'account_id'=>$account['account_id'],
                    'department_id'=>$department,
                    'budget'=>$b['budget']
                    ]
                );
            }
        }
        
        return response()->json($accounts)
                ->setStatusCode(201);
    }
    public function update(Request $request, $id){
        $decoid = decode($id);
        $budget = Budget::findOrFail($decoid);

        validate($request->all(), [
            'account_chart_id' => 'required',
            'budget_total' => 'required',
        ]);

        $budget->account_chart_id = decode($request->account_chart_id);
        $budget->department_id = decode($request->department_id);
        $budget->budget_total = $request->budget_total;
        $budget->budget_01 = $request->budget_01;
        $budget->budget_02 = $request->budget_02;
        $budget->budget_03 = $request->budget_03;
        $budget->budget_04 = $request->budget_04;
        $budget->budget_05 = $request->budget_05;
        $budget->budget_06 = $request->budget_06;
        $budget->budget_07 = $request->budget_07;
        $budget->budget_08 = $request->budget_08;
        $budget->budget_09 = $request->budget_09;
        $budget->budget_10 = $request->budget_10;
        $budget->budget_11 = $request->budget_11;
        $budget->budget_12 = $request->budget_12;
        $budget->update();
        return new BudgetResource($budget);
    }
    public function delete($id)
    {
        $id = decode($id);
        $budget = Budget::findOrFail($id);
        $budget->delete();
        return response()->json(null, 204);
    }
    public function getPeriod(){
        $user = Auth::user();
        $period = $user->company->accounting_period;
    }
}