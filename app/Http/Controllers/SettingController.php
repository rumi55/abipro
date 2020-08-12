<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(){
        return view('setting.index');
    }
    public function indexSave(Request $request){
        $settings = $request->all();
        $company_id=company('id');
        foreach($settings as $key =>$value){
            if($key=='_token'){
                continue;
            }
            \DB::table('company_settings')->updateOrInsert([
                'key'=>$key, 'company_id'=>$company_id
            ], [
                'value'=>$value
            ]);
        }
        return redirect()->back()->with('success', __('General settings have been saved.'));
    }
    public function accountMapping(){
        $mappings = \App\AccountMapping::orderBy('id')->get();
        $account_mappings = \App\Account::where('company_id', company('id'))->whereNotNull('account_mapping')->pluck('id','account_mapping')->toArray();
        return view('setting.account_mapping', compact('mappings', 'account_mappings'));
    }
    public function accountMappingSave(Request $request){
        // dd($request->all());
        $company_id = company('id');
        $mappings = \App\AccountMapping::orderBy('id')->get();
        foreach ($mappings as $map) {
            //cek ke akun apakah udah ada mapping
            $account = \App\Account::where('company_id', $company_id)->where('account_mapping', $map->id)->first();
            //apakah ada request mapping ini
            $field = 'mapping_'.$map->id;
            $account_id = $request->$field;
            if($account!=null){
                $account->account_mapping = null;
                $account->save();
            }
            if(!empty($account_id)){
                $naccount = \App\Account::find($account_id);
                $naccount->account_mapping = $map->id;
                $naccount->save();

            }
        }
        return redirect()->route('settings.account_mapping')->with('success', trans(':attr has been saved.', ['attr'=>'Account Mapping']));
    }
}
