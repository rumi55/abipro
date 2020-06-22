<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\CurrencyResource;
use App\Exceptions\ApiValidationException;
use App\Currency;
use Auth;
use Validator;

class CurrencyController extends Controller
{
    public function getAll(Request $request){
        $page_size = $request->query('page_size', Currency::count());
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter');
        $currency = Currency::whereRaw('1=1');
        if(isset($filter)){
            foreach($filter as $column => $value){
                $currency = $currency->where($column,'like', "%$value%");
            }
        }
        if(!empty($search)){
            $currency = $currency->where('name','like', "%$search%");
            $currency = $currency->orWhere('symbol','like', "%$search%");
            $currency = $currency->orWhere('code','like', "%$search%");
        }
        
        if(!empty($sort_key)){
            $currency = $currency->orderBy($sort_key, $sort_order);
        }        
        $currency = $currency->paginate($page_size)->appends($request->query());
        return CurrencyResource::collection($currency);
    }
    public function get($id){
        $id = decode($id);
        return new CurrencyResource(Currency::findOrFail($id));
    }
    
    public function create(Request $request){
        validate($request->all(), [
            'code' => 'required|unique:currencies,code',
            'name' => 'required|string',
        ]);

        $currency = Currency::create($request->all());

        return (new CurrencyResource($currency))
                ->response()
                ->setStatusCode(201);
    }
    public function update(Request $request, $id){
        $decoid = decode($id);
        $currency = Currency::findOrFail($decoid);

        validate($request->all(), [
            'code' => 'required|unique:currencies,code',
            'name' => 'required|string',
        ]);

        $currency->name = $request->name;
        $currency->code = $request->code;
        $currency->symbol = $request->symbol;
        $currency->update();
        return new CurrencyResource($currency);
    }
    public function delete($id)
    {
        $id = decode($id);
        $currency = Currency::findOrFail($id);
        $currency->delete();
        return response()->json(null, 204);
    }
}