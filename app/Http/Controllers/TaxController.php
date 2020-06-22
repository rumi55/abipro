<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\TaxResource;
use App\Exceptions\ApiValidationException;
use App\Tax;
use Auth;
use Validator;

class TaxController extends Controller
{
    public function getAll(Request $request){
        $company_id = Auth::user()->activeCompany()->id;
        $page_size = $request->query('page_size', 10);
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter');
        $tax = Tax::where('company_id', $company_id);
        if(isset($filter)){
            foreach($filter as $column => $value){
                $tax = $tax->where($column,'like', "%$value%");
            }
        }
        if(!empty($search)){
            $tax = $tax->where('name','like', "%$search%");
        }
        $sort = $request->query('sort');
        if(!empty($sort)){
            $sort = explode('-',$sort);
            $sort_key=$sort[0];
            $sort_order=count($sort)==2?(substr($sort[1], 0, 3)=='asc'?'asc':'desc'):'asc';
            $tax = $tax->orderBy($sort_key, $sort_order);
        }
        
        if(!empty($sort_key)){
            $tax = $tax->orderBy($sort_key, $sort_order);
        }        
        $tax = $tax->paginate($page_size)->appends($request->query());
        return TaxResource::collection($tax);
    }
    public function get($id){
        $id = decode($id);
        $tax = Tax::findOrFail($id);
        return new TaxResource($tax);
    }
    
    public function create(Request $request){
        $company_id = Auth::user()->activeCompany()->id;
        validate($request->all(), [
            'name' => 'required|max:128|min:2',
            'percentage' => 'required',
            'buy_account_id' => 'required',
            'sales_account_id' => 'required',
        ]);
        
        $tax = Tax::create([
            'company_id' => $company_id,
            'created_by' => Auth::user()->id,
            'name'=>$request->name,
            'percentage'=>$request->percentage,
            'buy_account_id'=>decode($request->buy_account_id),
            'sales_account_id'=>decode($request->sales_account_id),
        ]);

        return (new TaxResource($tax))
                ->response()
                ->setStatusCode(201);
    }
    public function update(Request $request, $id){
        $decoid = decode($id);
        $tax = Tax::findOrFail($decoid);

        validate($request->all(), [
            'name' => 'required|max:128|min:2',
            'percentage' => 'required',
            'buy_account_id' => 'required',
            'sales_account_id' => 'required',
        ]);

        $tax->name = $request->name;
        $tax->percentage = $request->percentage;
        $tax->buy_account_id = decode($request->buy_account_id);
        $tax->sales_account_id = decode($request->sales_account_id);
        $tax->updated_by = Auth::user()->id;
        $tax->update();
        return new TaxResource($tax);
    }
    public function delete($id)
    {
        $id = decode($id);
        $tax = Tax::findOrFail($id);
        $tax->delete();
        return response()->json(null, 204);
    }
}