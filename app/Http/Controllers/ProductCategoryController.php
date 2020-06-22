<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\ProductCategoryResource;
use App\Exceptions\ApiValidationException;
use App\ProductCategory;
use Auth;
use Validator;

class ProductCategoryController extends Controller
{
    public function getAll(Request $request){
        $company_id = Auth::user()->activeCompany()->id;
        $category = ProductCategory::where('company_id', $company_id);
        $page_size = $request->query('page_size', $category->count());
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter');
        if(isset($filter)){
            foreach($filter as $column => $value){
                $category = $category->where($column,'like', "%$value%");
            }
        }
        if(!empty($search)){
            $category = $category->where(function ($query) use($search){
                $query->where('name','like', "%$search%");
            });
        }
        
        if(!empty($sort_key)){
            $category = $category->orderBy($sort_key, $sort_order);
        }
        $sort = $request->query('sort');
        if(!empty($sort)){
            $sort = explode('-',$sort);
            $sort_key=$sort[0];
            $sort_order=count($sort)==2?(substr($sort[1], 0, 3)=='asc'?'asc':'desc'):'asc';
            $category = $category->orderBy($sort_key, $sort_order);
        }  
        if(empty($sort_key) && empty($sort)){
            $category = $category->orderBy('name', 'asc');
        }      
        $category = $category->paginate($page_size)->appends($request->query());
        return ProductCategoryResource::collection($category);
    }
    public function get($id){
        $id = decode($id);
        return new ProductCategoryResource(ProductCategory::findOrFail($id));
    }
    
    public function create(Request $request){
        validate($request->all(), [
            'name' => 'required',
        ]);
        
        $data = array_merge($request->all(),[
            'company_id'=>Auth::user()->activeCompany()->id,
            'created_by'=>Auth::user()->id
        ]);    
        $category = ProductCategory::create($data);

        return (new ProductCategoryResource($category))
                ->response()
                ->setStatusCode(201);
    }
    public function update(Request $request, $id){
        $decoid = decode($id);
        $category = ProductCategory::findOrFail($decoid);
        validate($request->all(), [
            'name' => 'required'
        ]);

        $category->name = $request->name;
        $category->updated_by = Auth::user()->id;
        $category->save();
        return new ProductCategoryResource($category);
    }
    
    public function delete($id)
    {
        $id = decode($id);
        $category = ProductCategory::findOrFail($id);
        $category->delete();
        return response()->json(null, 204);
    }
}
