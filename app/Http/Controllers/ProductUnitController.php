<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\ProductUnitResource;
use App\Exceptions\ApiValidationException;
use App\ProductUnit;
use Auth;
use Validator;

class ProductUnitController extends Controller
{
    public function getAll(Request $request){
        $company_id = Auth::user()->activeCompany()->id;
        $unit = ProductUnit::where('company_id', $company_id);
        $page_size = $request->query('page_size', $unit->count());
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter');
        if(isset($filter)){
            foreach($filter as $column => $value){
                $unit = $unit->where($column,'like', "%$value%");
            }
        }
        if(!empty($search)){
            $unit = $unit->where(function ($query) use($search){
                $query->where('name','like', "%$search%");
            });
        }
        
        if(!empty($sort_key)){
            $unit = $unit->orderBy($sort_key, $sort_order);
        }
        $sort = $request->query('sort');
        if(!empty($sort)){
            $sort = explode('-',$sort);
            $sort_key=$sort[0];
            $sort_order=count($sort)==2?(substr($sort[1], 0, 3)=='asc'?'asc':'desc'):'asc';
            $unit = $unit->orderBy($sort_key, $sort_order);
        }  
        if(empty($sort_key) && empty($sort)){
            $unit = $unit->orderBy('name', 'asc');
        }      
        $unit = $unit->paginate($page_size)->appends($request->query());
        return ProductUnitResource::collection($unit);
    }
    public function get($id){
        $id = decode($id);
        return new ProductUnitResource(ProductUnit::findOrFail($id));
    }
    
    public function create(Request $request){
        validate($request->all(), [
            'name' => 'required',
        ]);
        
        $data = array_merge($request->all(),[
            'company_id'=>Auth::user()->activeCompany()->id,
            'created_by'=>Auth::user()->id
        ]);    
        $unit = ProductUnit::create($data);

        return (new ProductUnitResource($unit))
                ->response()
                ->setStatusCode(201);
    }
    public function update(Request $request, $id){
        $decoid = decode($id);
        $unit = ProductUnit::findOrFail($decoid);
        validate($request->all(), [
            'name' => 'required'
        ]);

        $unit->name = $request->name;
        $unit->updated_by = Auth::user()->id;
        $unit->save();
        return new ProductUnitResource($unit);
    }
    
    public function delete($id)
    {
        $id = decode($id);
        $unit = ProductUnit::findOrFail($id);
        $unit->delete();
        return response()->json(null, 204);
    }
}
