<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\WarehouseResource;
use App\Exceptions\ApiValidationException;
use App\Warehouse;
use Auth;
use Validator;

class WarehouseController extends Controller
{
    public function getAll(Request $request){
        $company_id = Auth::user()->activeCompany()->id;
        $page_size = $request->query('page_size', 10);
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter');
        $warehouse = Warehouse::where('company_id', $company_id);
        if(isset($filter)){
            foreach($filter as $column => $value){
                $warehouse = $warehouse->where($column,'like', "%$value%");
            }
        }
        if(!empty($search)){
            $warehouse = $warehouse->where('name','like', "%$search%");
            $warehouse = $warehouse->orWhere('custom_id','like', "%$search%");
        }
        $sort = $request->query('sort');
        if(!empty($sort)){
            $sort = explode('-',$sort);
            $sort_key=$sort[0];
            $sort_order=count($sort)==2?(substr($sort[1], 0, 3)=='asc'?'asc':'desc'):'asc';
            $warehouse = $warehouse->orderBy($sort_key, $sort_order);
        }
        
        if(!empty($sort_key)){
            $warehouse = $warehouse->orderBy($sort_key, $sort_order);
        }        
        $warehouse = $warehouse->paginate($page_size)->appends($request->query());
        return WarehouseResource::collection($warehouse);
    }
    public function get($id){
        $id = decode($id);
        $warehouse = Warehouse::findOrFail($id);
        return new WarehouseResource($warehouse);
    }
    
    public function create(Request $request){
        $company_id = Auth::user()->activeCompany()->id;
        validate($request->all(), [
            'name' => 'required|max:128|min:2',
        ]);
        
        $warehouse = Warehouse::create([
            'company_id' => $company_id,
            'created_by' => Auth::user()->id,
            'custom_id'=>$request->custom_id,
            'name'=>$request->name
        ]);

        return (new WarehouseResource($warehouse))
                ->response()
                ->setStatusCode(201);
    }
    public function update(Request $request, $id){
        $decoid = decode($id);
        $warehouse = Warehouse::findOrFail($decoid);

        validate($request->all(), [
            'name' => 'required|max:128|min:2',
        ]);

        $warehouse->name = $request->name;
        $warehouse->custom_id = $request->custom_id;
        $warehouse->updated_by = Auth::user()->id;
        $warehouse->update();
        return new WarehouseResource($warehouse);
    }
    public function delete($id)
    {
        $id = decode($id);
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->delete();
        return response()->json(null, 204);
    }
}