<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\AccountTypeResource;
use App\Exceptions\ApiValidationException;
use App\AccountType;
use Auth;
use Validator;
use PDF;

class AccountTypeController extends Controller
{
    public function getAll(Request $request){
        return $this->query($request);
    }
    public function pdfTable(Request $request){
        $data = $this->query($request);
        $pdf = PDF::loadView('pdf.account_type', array('data'=>$data));
        return $pdf->download('tipe_akun.pdf');
    }   
    private function query(Request $request){
        $accountType = AccountType::whereRaw('1=1');
        $page_size = $request->query('page_size', $accountType->count());
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter');
        if(isset($filter)){
            foreach($filter as $column => $value){
                $accountType = $accountType->where($column,'like', "%$value%");
            }
        }
        if(!empty($search)){
            $accountType = $accountType->where(function ($query) use($search){
                $query->where('name','like', "%$search%")
                ->orWhere('id','like', "%$search%");
            });
        }
        
        if(!empty($sort_key)){
            $accountType = $accountType->orderBy($sort_key, $sort_order);
        }
        //sort=column-asc
        $sort = $request->query('sort');
        if(!empty($sort)){
            $sort = explode('-',$sort);
            $sort_key=$sort[0];
            $sort_order=count($sort)==2?(substr($sort[1], 0, 3)=='asc'?'asc':'desc'):'asc';
            $accountType = $accountType->orderBy($sort_key, $sort_order);
        }  
        if(empty($sort_key) && empty($sort)){
            $accountType = $accountType->orderBy('id', 'asc');
        }      
        $accountType = $accountType->paginate($page_size)->appends($request->query());
        return AccountTypeResource::collection($accountType);
    }
    public function get($id){
        $id = decode($id);
        return new AccountTypeResource(AccountType::findOrFail($id));
    }
    
    public function create(Request $request){
        validate($request->all(), [
            'id' => 'required|min:2',
            'name' => 'required',
            'account_id' => 'required|min:2|max:2'
        ]);

        $accountType = AccountType::create($request->all());

        return (new AccountTypeResource($accountType))
                ->response()
                ->setStatusCode(201);
    }
    public function update(Request $request, $id){
        $decoid = decode($id);
        $accountType = AccountType::findOrFail($decoid);
        validate($request->all(), [
            'name' => 'required',
            'id' => 'required|min:2',
            'account_id' => 'required|min:2|max:2'
        ]);

        $accountType->name = $request->name;
        $accountType->code = $request->code;
        $accountType->account_id = $request->account_id;
        $accountType->save();
        return new AccountTypeResource($accountType);
    }
    
    public function delete($id)
    {
        $id = decode($id);
        $accountType = AccountType::findOrFail($id);
        $accountType->delete();
        return response()->json(null, 204);
    }
}