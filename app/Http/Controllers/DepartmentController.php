<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\DepartmentResource;
use App\Exceptions\ApiValidationException;
use App\Department;
use Auth;
use Validator;

class DepartmentController extends Controller
{
    public function index(){
        $data = dcru_dt('departments', 'dtables');
        return view('company.department.index', $data);
    }
    public function create(){
        $model = new Department;
        $mode = 'create';
        return view('company.department.form', compact('model', 'mode'));
    }
    public function edit($id){
        $model = Department::findOrFail($id);
        $mode = 'edit';
        return view('company.department.form', compact('model', 'mode'));
    }
    public function save(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();

        $data = $request->all();
        $rules = [
            'custom_id' => 'required|max:16|unique:departments,custom_id,NULL,id,company_id,'.$company->id,
            'name' => 'required|max:64|unique:departments,name,NULL,id,company_id,'.$company->id,
            'description' => 'max:128'
        ];
        $attr = [
            'name' => trans('Department'),
            'custom_id' => trans('ID'),
            'description' => trans('Description')
        ];
        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $department = Department::create([
            'company_id' => $company->id,
            'custom_id'=>$request->custom_id,
            'name'=>$request->name,
            'description'=>$request->description
        ]);
        add_log('departments', 'create', '');
        return redirect()->route('departments.index')->with('success', 'Departemen baru berhasil ditambahkan.');
    }
    public function update(Request $request, $id){
        $user = Auth::user();
        $company = $user->activeCompany();

        $data = $request->all();
        $rules = [
            'custom_id' => 'required|max:16|unique:departments,custom_id,'.$id.',id,company_id,'.$company->id,
            'name' => 'required|max:64|unique:departments,name,'.$id.',id,company_id,'.$company->id,
            'description' => 'max:128'
        ];
        
        $attr = [
            'name' => trans('Department'),
            'custom_id' => trans('ID'),
            'description' => trans('Description')
        ];
        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $department = Department::findOrFail($id);
        $department->name = $request->name;
        $department->custom_id = $request->custom_id;
        $department->description = $request->description;
        $department->update();
        add_log('departments', 'update', '');
        return redirect()->route('departments.index')->with('success', trans('Changes have been saved.'));
    }
    public function delete($id)
    {
        $department = Department::findOrFail($id);
        $name = $department->name;
        if($department->isLocked()){
            return redirect()->route('departments.index')->with('error', 'Departemen '.$name.' tidak dapat dihapus karena digunakan dalam transaksi.');
        }
        $department->delete();
        add_log('departments', 'delete', '');
        return redirect()->route('departments.index')->with('success', 'Departemen '.$name.' telah dihapus.');
    }
}