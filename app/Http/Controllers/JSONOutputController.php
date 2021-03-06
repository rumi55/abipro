<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Product;
use App\Account;
use App\Http\Resources\AccountResource;
use Auth;

class JSONOutputController extends Controller
{
    public function index(Request $request, $name)
    {

        return $this->$name($request);
    }
    public function account_types(Request $request)
    {
        return \App\AccountType::all();
    }
    public function accounts(Request $request)
    {
        $company_id = company('id');
        $account = Account::where('company_id', $company_id);
        $page_size = $request->query('page_size', $account->count());
        // $page_size = $request->query('page_size', 10);
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $search = $request->query('q');
        $filter = $request->query('filter');
        if (isset($filter)) {
            foreach ($filter as $column => $value) {
                if ($column == 'group') {
                    $group = explode(',', $value);
                    $account = $account->whereHas('accountType', function ($q) use ($group) {
                        $q->whereIn('group', $group);
                    });
                } else {
                    $account = $account->where($column, '=', $value);
                }
            }
        }
        // if(!isset($filter['has_children'])){
        //     $account = $account->whereNull('account_parent_id');
        // }

        $sort = $request->query('sort');
        if (!empty($sort)) {
            $sort = explode('-', $sort);
            $sort_key = $sort[0];
            $sort_order = count($sort) == 2 ? (substr($sort[1], 0, 3) == 'asc' ? 'asc' : 'desc') : 'asc';
            $account = $account->orderBy($sort_key, $sort_order);
        }
        if (!empty($search)) {
            $account = $account->where(function ($query) use ($search) {
                $query->where('account_name', 'like', "%$search%")
                    ->orWhere('account_no', 'like', "%$search%")
                    ->orWhereHas('parent', function ($q) use ($search) {
                        $q->where('account_name', 'like', "%$search%")
                            ->orWhere('account_no', 'like', "%$search%");
                    })
                    ->orWhereHas('accountType', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }

        if (!empty($sort_key)) {
            $account = $account->orderBy($sort_key, $sort_order);
        }
        if (empty($sort_key) && empty($sort)) {
        }
        // $account = $account->orderBy('account_no', 'asc');
        // $account = $account->orderBy('account_type_id', 'asc');
        $account = $account->orderBy('sequence', 'asc');

        // $data = $account->paginate($page_size)->appends($request->query());
        $data = $account->paginate($page_size)->appends($request->query());
        return AccountResource::collection($data);
    }

    public function products(Request $request)
    {
        $company_id = company('id');
        $product = Product::where('company_id', $company_id);
        $page_size = $request->query('page_size', $product->count());
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter');
        if (isset($filter)) {
            foreach ($filter as $column => $value) {
                $product = $product->where($column, 'like', "%$value%");
            }
        }
        if (!empty($search)) {
            $product = $product->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('custom_id', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        if (!empty($sort_key)) {
            $product = $product->orderBy($sort_key, $sort_order);
        }
        //sort=column-asc
        $sort = $request->query('sort');
        if (!empty($sort)) {
            $sort = explode('-', $sort);
            $sort_key = $sort[0];
            $sort_order = count($sort) == 2 ? (substr($sort[1], 0, 3) == 'asc' ? 'asc' : 'desc') : 'asc';
            $product = $product->orderBy($sort_key, $sort_order);
        }
        if (empty($sort_key) && empty($sort)) {
            $product = $product->orderBy('name', 'asc');
        }
        $product = $product->paginate($page_size)->appends($request->query());
        return ProductResource::collection($product);
    }

    public function taxes(Request $request)
    {
        $company_id = company('id');
        $page_size = $request->query('page_size', 10);
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter');
        $tax = \App\Tax::where('company_id', $company_id);
        if (isset($filter)) {
            foreach ($filter as $column => $value) {
                $tax = $tax->where($column, 'like', "%$value%");
            }
        }
        if (!empty($search)) {
            $tax = $tax->where('name', 'like', "%$search%");
        }
        $sort = $request->query('sort');
        if (!empty($sort)) {
            $sort = explode('-', $sort);
            $sort_key = $sort[0];
            $sort_order = count($sort) == 2 ? (substr($sort[1], 0, 3) == 'asc' ? 'asc' : 'desc') : 'asc';
            $tax = $tax->orderBy($sort_key, $sort_order);
        }

        if (!empty($sort_key)) {
            $tax = $tax->orderBy($sort_key, $sort_order);
        }
        $tax = $tax->paginate($page_size)->appends($request->query());
        return \App\Http\Resources\TaxResource::collection($tax);
    }
    public function contacts(Request $request)
    {
        $company_id = company('id');
        $contact = \App\Contact::where('company_id', $company_id);
        $page_size = $request->query('page_size', $contact->count());
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $search = $request->query('q');
        $filter = $request->query('filter');
        $types = $request->query('type'); //customer,supplier,employee,others

        if (isset($types)) {
            $types = explode(',', $types);
            foreach ($types as $type) {
                $contact = $contact->where('is_' . $type, 1);
            }
        }
        if (isset($filter)) {
            foreach ($filter as $column => $value) {
                $contact = $contact->where($column, 'like', "%$value%");
            }
        }
        if (!empty($search)) {
            $contact = $contact->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('fax', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        }

        if (!empty($sort_key)) {
            $contact = $contact->orderBy($sort_key, $sort_order);
        }
        //sort=column-asc
        $sort = $request->query('sort');
        if (!empty($sort)) {
            $sort = explode('-', $sort);
            $sort_key = $sort[0];
            $sort_order = count($sort) == 2 ? (substr($sort[1], 0, 3) == 'asc' ? 'asc' : 'desc') : 'asc';
            $contact = $contact->orderBy($sort_key, $sort_order);
        }
        if (empty($sort_key) && empty($sort)) {
            $contact = $contact->orderBy('name', 'asc');
        }
        $contact = $contact->paginate($page_size)->appends($request->query());
        return \App\Http\Resources\ContactResource::collection($contact);
    }
    public function departments(Request $request)
    {
        $company_id = company('id');
        return \App\Department::where('company_id', $company_id)->get();
    }
    public function numberings(Request $request)
    {
        $company_id = company('id');
        $numberings = \App\Numbering::where('company_id', $company_id);
        $search = $request->query('q');
        $filter = $request->query('filter');
        if (isset($filter)) {
            foreach ($filter as $column => $value) {
                $numberings = $numberings->where($column, 'like', "%$value%");
            }
        }
        if (isset($request->type)) {
            $numberings = $numberings->where('transaction_type_id', $request->type);
        }
        if (!empty($search)) {
            $numberings = $numberings->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('format', 'like', "%$search%");
            });
        }
        return $numberings->get();
    }
    public function terms(Request $request)
    {
        $company_id = company('id');
        return \App\Term::where('company_id', $company_id)->get();
    }
    public function units(Request $request)
    {
        $company_id = company('id');
        return \App\ProductUnit::where('company_id', $company_id)->get();
    }

    public function unique(Request $request)
    {
        $table = $request->table;
        $column = $request->column;
        $company_id = company('id');
        return \DB::table($table)->where('company_id', $company_id)->select($column)->distinct()->pluck($column)->toArray();
    }
    public function search(Request $request)
    {
        $table = $request->table;
        $column = $request->column;
        $q = $request->q;
        $company_id = company('id');
        return \DB::table($table)->where('company_id', $company_id)
            ->where($column, 'like', "%$q%")
            ->select($column)->distinct()->pluck($column)->toArray();
    }

    public function getTransactionNumber($id, $type='journal')
    {
        $company_id = company('id');

        $numbering = \App\Numbering::where('id',$id)->where('company_id', $company_id)->first();
        if($numbering==null){
            return response()->json(['status'=>'success', 'data'=>null, 'message'=>'Numbering format tot found']);
        }

        if ($numbering->counter_reset == 'y') {
            $period = date('Y');
        } else if ($numbering->counter_reset == 'm') {
            $period = date('Y-m');
        } else if ($numbering->counter_reset == 'd') {
            $period = date('Y-m-d');
        } else {
            $period  = null;
        }
        $counter = \App\Counter::firstOrCreate(
            ['period' => $period, 'numbering_id' => $numbering->id, 'company_id' => $company_id],
            ['counter' => $numbering->counter_start - 1]
        );
        $trans_no = null;
        $check = true;
        do {
            $counter->getNumber();
            $trans_no = $counter->last_number;
            if($type=='journal'){
                $check = \App\Journal::where('trans_no', $trans_no)->where('company_id', $company_id)->exists();
            }else if($type=='voucher'){
                $check = \App\Transaction::where('trans_no', $trans_no)->where('company_id', $company_id)->exists();
            }else if($type=='contact'){
                $check = \App\Contact::where('custom_id', $trans_no)->where('company_id', $company_id)->exists();
            }
        } while ($check);
        if($trans_no==null){
            return response()->json(['status'=>'success', 'trans_no'=>null, 'message'=>'Numbering format tot found']);
        }
        return response()->json(['status'=>'success', 'trans_no'=>$trans_no]);
    }
}
