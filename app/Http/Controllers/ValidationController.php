<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
class ValidationController extends Controller
{
    public function unique(Request $request){
        $id = $request->id;
        
        $table = $request->entity;
        $field = $request->field;
        $value = $request->value;
        $company = Auth::user()->activeCompany();
        if(empty($table)||empty($field)){
            return ['status'=>true];
        }
        $exist = DB::table($table)->where('company_id', $company->id);
        if(!($id=='0' || empty($id))){
            $id = decode($id);
            $exist = $exist->where('id', '<>',$id);
        }
        $exist = $exist->where($field, $value)
        ->exists();
        if($exist){
            return ['status'=>true];
        }else{
            return ['status'=>false];
        }
    }
}
