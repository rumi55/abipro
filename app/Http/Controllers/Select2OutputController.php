<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
class Select2OutputController extends Controller
{
    public function get(Request $request, $name){
        return $this->$name($request);
    }

    public function journals(Request $request){
        $company_id = \Auth::user()->activeCompany()->id;
        $query = \DB::table('journals')->where('company_id', '=', $company_id)
        ->selectRaw('id, trans_no as text')
        ->where('is_processed', 1)->orderBy('trans_date', 'desc');
        if(isset($request->search)){
            $q = $request->search;
            $query = $query->whereRaw("trans_no like '%$q%'");
        }
        return $query->get();
    }
    public function vouchers(Request $request){
        $company_id = \Auth::user()->activeCompany()->id;
        $query = \DB::table('journals')->where('company_id', '=', $company_id)
        ->selectRaw('id, trans_no as text')
        ->where('is_voucher', 1)->where('is_processed', 0)->where('status', 'approved')->orderBy('trans_date', 'desc');
        if(isset($request->search)){
            $q = $request->search;
            $query = $query->whereRaw("trans_no like '%$q%'");
        }
        return $query->get();
    }
    public function accounts(Request $request){
        $company_id = \Auth::user()->activeCompany()->id;
        $query = \DB::table('accounts')
        ->where('company_id', '=', $company_id)
        ->selectRaw("id, (concat('(',account_no,') ',account_name)) as text, account_type_id, account_parent_id");
        if(isset($request->has_children)){
            $query = $query->where('has_children', filter_var($request->has_children, FILTER_VALIDATE_BOOLEAN));
        }
        if(isset($request->parent_id)){
            $query = $query->where('account_parent_id', $request->parent_id);
        }
        if(isset($request->tree_level)){
            $query = $query->where('tree_level', $request->tree_level);
        }
        if(isset($request->type_id)){
            $query = $query->where('account_type_id', $request->type_id);
        }
        if(isset($request->search)){
            $q = $request->search;
            $query = $query->whereRaw("trans_no like '%$q%'");
        }
        return $query->orderByRaw('sequence, account_type_id')->get();
    }
    public function numberings(Request $request){
        $company_id = \Auth::user()->activeCompany()->id;
        $query = \DB::table('numberings')->where('company_id', '=', $company_id);
        if(!empty($request->transaction_type)){
            $query = $query->where('transaction_type_id', $request->transaction_type);
        }
        $query = $query->selectRaw('id, name as text')
        ->orderBy('name');
        return $query->get();
    }
    public function departments(Request $request){
        $company_id = \Auth::user()->activeCompany()->id;
        $query = \DB::table('departments')->where('company_id', '=', $company_id)
        ->selectRaw('id, name as text')
        ->orderBy('name');
        return $query->get();
    }
    public function users(Request $request){
        $company_id = \Auth::user()->activeCompany()->id;
        $query = \DB::table('company_users')->join('users', 'users.id', '=', 'user_id')->where('company_id', '=', $company_id)
        ->selectRaw('users.id as id, name as text')
        ->orderBy('name');
        $users = $query->get();
        $owner = \DB::table('companies')->join('users', 'users.id', '=', 'companies.owner_id')->where('companies.id', '=', $company_id)
        ->selectRaw('users.id as id, users.name as text')
        ->orderBy('users.name')->get();
        return array_merge($owner->toArray(), $users->toArray());
    }
    public function sortirs(Request $request){
        $company_id = \Auth::user()->activeCompany()->id;
        $grouped = empty($request->grouped)?true:filter_var($request->grouped, FILTER_VALIDATE_BOOLEAN);
        $query = \DB::table('tags')->where('company_id', '=', $company_id);
        if(!empty($request->fields)){
            $fields = explode(',',$request->fields);
            $select = '';
            $cfields = count($fields);
            $grouped = $cfields==2?false:$grouped;
            foreach($fields as $i=>$field){
                $select.=$i==0?('`'.$field.'` as id'):($i==1?('`'.$field.'` as text'):'');
                if($i==2 && $group){
                    $select.='`'.$field.'` as grup';
                }
                $select.=$i<$cfields-1?', ':'';
            }
            $query = $query->selectRaw($select);
        }else{
            $query = $query->selectRaw('id, `group` as grup, item_name as text');
        }

        $results = $query->orderByRaw('`group`')->distinct()->get();

        if($grouped){
            $options = [];
            $group = null;
            $c = count($results);
            foreach($results as $r =>$res){
                if($group!=$res->grup){
                    $group = $res->grup;
                    $optgroup = [];
                    $optgroup['text'] = $res->grup;
                    $optgroup['children'][] = ['id'=>$res->id, 'text'=>$res->text];
                }else{
                    $optgroup['children'][] = ['id'=>$res->id, 'text'=>$res->text];
                }
                if($r+1<$c){
                    if(($results[$r])->grup!=($results[$r+1])->grup){
                        $options[] = $optgroup;
                    }
                }else if($r==$c-1){
                    $options[] = $optgroup;
                }
            }
            return $options;
        }
        return $results;

    }
}
