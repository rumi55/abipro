<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\TransactionTypeResource;
use App\Exceptions\ApiValidationException;
use App\TransactionType;
use Auth;
use Validator;
use PDF;

class TransactionTypeController extends Controller
{
    public function getAll(Request $request){
        $model = TransactionType::whereRaw('1=1');
        $page_size = $request->query('page_size', $model->count());
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter');
        if(isset($filter)){
            foreach($filter as $column => $value){
                $model = $model->where($column,'like', "%$value%");
            }
        }
        if(!empty($search)){
            $model = $model->where('name','like', "%$search%");
            $model = $model->orWhere('code','like', "%$search%");
        }
        
        if(!empty($sort_key)){
            $model = $model->orderBy($sort_key, $sort_order);
        }
        //sort=column-asc
        $sort = $request->query('sort');
        if(!empty($sort)){
            $sort = explode('-',$sort);
            $sort_key=$sort[0];
            $sort_order=count($sort)==2?(substr($sort[1], 0, 3)=='asc'?'asc':'desc'):'asc';
            $model = $model->orderBy($sort_key, $sort_order);
        }  
        if(empty($sort_key) && empty($sort)){
            $model = $model->orderBy('id', 'asc');
        }      
        $model = $model->paginate($page_size)->appends($request->query());
        return TransactionTypeResource::collection($model);
    }
    public function get($id){
        $id = decode($id);
        return new TransactionTypeResource(TransactionType::findOrFail($id));
    }
}