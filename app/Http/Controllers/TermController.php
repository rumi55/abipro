<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\TermResource;
use App\Exceptions\ApiValidationException;
use App\Term;
use Auth;
use Validator;

class TermController extends Controller
{
    public function getAll(Request $request){
        $company_id = Auth::user()->activeCompany()->id;
        $page_size = $request->query('page_size', 10);
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter');
        $model = Term::where('company_id', $company_id);
        if(isset($filter)){
            foreach($filter as $column => $value){
                $model = $model->where($column,'like', "%$value%");
            }
        }
        if(!empty($search)){
            $model = $model->where('name','like', "%$search%");
        }
        $sort = $request->query('sort');
        if(!empty($sort)){
            $sort = explode('-',$sort);
            $sort_key=$sort[0];
            $sort_order=count($sort)==2?(substr($sort[1], 0, 3)=='asc'?'asc':'desc'):'asc';
            $model = $model->orderBy($sort_key, $sort_order);
        }
        
        if(!empty($sort_key)){
            $model = $model->orderBy($sort_key, $sort_order);
        }        
        $model = $model->paginate($page_size)->appends($request->query());
        return TermResource::collection($model);
    }
    public function get($id){
        $id = decode($id);
        $model = Term::findOrFail($id);
        return new TermResource($model);
    }
    
    public function create(Request $request){
        $company_id = Auth::user()->activeCompany()->id;
        validate($request->all(), [
            'name' => 'required|max:128|min:2',
            'period' => 'required',
        ]);
        
        $model = Term::create([
            'company_id' => $company_id,
            'created_by' => Auth::user()->id,
            'name'=>$request->name,
            'period'=>$request->period
        ]);

        return (new TermResource($model))
                ->response()
                ->setStatusCode(201);
    }
    public function update(Request $request, $id){
        $decoid = decode($id);
        $model = Term::findOrFail($decoid);

        validate($request->all(), [
            'name' => 'required|max:128|min:2',
            'period' => 'required',
        ]);

        $model->name = $request->name;
        $model->period = $request->period;
        $model->updated_by = Auth::user()->id;
        $model->update();
        return new TermResource($model);
    }
    public function delete($id)
    {
        $id = decode($id);
        $model = Term::findOrFail($id);
        $model->delete();
        return response()->json(null, 204);
    }
}