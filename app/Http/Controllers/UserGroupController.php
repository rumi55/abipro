<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserGroupResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyUserGroupResource;
use App\Exceptions\ApiValidationException;
use App\User;
use App\UserGroup;
use App\Company;
use App\JournalType;
use Auth;
use Validator;
use Str;
use Carbon\Carbon;


class UserGroupController extends Controller
{
    public function index(){
        $data = dcru_dt('user_groups', 'dtables');
        return view('user.user_group', $data);
    }
    public function create(){
        $group = new UserGroup;
        $mode = 'create';
        return view('user.form_group', compact('group', 'mode'));
    }
    public function edit($id){
        $group = UserGroup::findOrFail($id);
        $mode = 'edit';
        return view('user.form_group', compact('group', 'mode'));
    }
    
    public function save(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();

        $name = Str::slug($request->display_name);    
        $data = $request->all();
        $data['name'] = $name;
        $rules = [
            'name' => 'required|max:64|unique:user_groups,name,NULL,id,company_id,'.$company->id,
            'display_name' => 'required|max:64',
            'description' => 'max:255'
        ];
        $attr = [
            'name' => 'Nama Grup',
            'display_name' => 'Nama Grup',
            'description' => 'Keterangan'
        ];
        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $userGroup = UserGroup::updateOrCreate([
            'name' => $name,    
            'company_id' => $company->id
        ],[
            'display_name' => $request->display_name,
            'description' => $request->description
        ]);
        add_log('user_groups', 'create', '');
        return redirect()->route('user_groups.index')->with('success', 'Grup pengguna baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id){
        $user = Auth::user();
        $company = $user->activeCompany();

        $name = Str::slug($request->display_name);    
        $data = $request->all();
        $data['name'] = $name;
        $rules = [
            'name' => 'required|max:64|unique:user_groups,name,'.$id.',id,company_id,'.$company->id,
            'display_name' => 'required|max:64',
            'description' => 'max:255'
        ];
        $attr = [
            'name' => 'Nama Grup',
            'display_name' => 'Nama Grup',
            'description' => 'Keterangan'
        ];
        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $userGroup = UserGroup::findOrFail($id);

        $userGroup->name = $name;
        $userGroup->display_name = $request->display_name;
        $userGroup->description = $request->description;
        $userGroup->update();
        add_log('user_groups', 'update', '');
        return redirect()->route('user_groups.index')->with('success', 'Perubahan grup pengguna telah disimpan.');
    }
    public function delete($id)
    {
        $id = decode($id);
        $userGroup = UserGroup::findOrFail($id);
        $name = $userGroup->display_name;
        $userGroup->delete();
        add_log('user_groups', 'delete', '');
        return redirect()->route('user_groups.index')->with('success', "Grup penguna $name telah dihapus");
    }
}