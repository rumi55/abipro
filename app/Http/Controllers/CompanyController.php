<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyTypeResource;
use App\Exceptions\ApiValidationException;
use App\Company;
use App\CompanyType;
use App\UserGroup;
use App\Account;
use Auth;
use Validator;
use DB;
use Str;

class CompanyController extends Controller
{
    public function index(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();
        $companies = Company::where('owner_id', $company->owner_id)
        ->orderBy('is_active', 'desc')
        ->orderBy('name')
        ->get();
        return view('company.index', compact('companies', 'user'));
    }
    public function setActive($id){
        $company = Company::findOrFail($id);
        $activeCompany = Auth::user()->activeCompany();
        // dd($company);
        $activeCompany->is_active = false;
        $company->is_active = true;
        $activeCompany->save();
        $company->save();
        return redirect()->route('companies.index');
    }
    public function get($id){
        $id = decode($id);
        return new CompanyResource(Company::findOrFail($id));
    }

    public function getActiveCompany(){
        $user = Auth::user();
        return new CompanyResource($user->activeCompany());
    }

    public function profile(){
        $company = Auth::user()->activeCompany();
        return view('company.profile', compact('company'));
    }
    public function profileEdit(){
        $company = Auth::user()->activeCompany();
        return view('company.form_profile', compact('company'));
    }
    public function profileUpdate(Request $request){
        $company = Auth::user()->activeCompany();
        $rules = [
            'name' => 'required|string',
            'email' => 'nullable|string|email',
            'phone' => 'nullable|string',
            'website' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,bmp,png,jpg|max:256'
        ];
        
        $attr = [
            'name' => 'Nama',
            'email' => 'Email',
            'phone' => 'Telepon',
            'logo' => 'Logo Perusahaan',
        ];

        $validator = \Validator::make($request->all(), $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            // dd($validator);
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $filename = \Str::slug($request->name.' '.date('Y m d H i s').' '.time(),'-');
        $filename = upload_file('logo', $filename, 'public/company');        
        $old_photo = '';
        if(!empty($filename)){
            $old_photo = $company->photo;
            $company->logo = $filename;
        }
        $company->name = $request->name;
        $company->email = $request->email??null;
        $company->phone = $request->phone??null;
        $company->website = $request->website??null;
        $company->address = $request->address??null;
        $company->shipping_address = $request->shipping_address??null;
        $company->save();
        if(!empty($old_photo)){
            \Storage::delete($old_photo);    
        }
        return redirect()->route('company.profile')->with('success', 'Profil perusahaan telah diubah.');
    }

    
    public function register(Request $request){
        $types = \App\CompanyType::all();
        return view('company.register', compact('types'));
    }
    
    public function create(Request $request){
        validate($request->all(), [
            'name' => 'required|string',
            'company_type_id' => 'required|string',
            // 'accounting_period' => 'required',
        ]);
        $user = Auth::user();
        $data = array_merge($request->all(), [
            'owner_id'=>$user->id,
            'accounting_start_date'=>date('Y-m-d')
        ]);
        //dd($data);
        try{
            DB::beginTransaction();
            $company = Company::create($data);
            //create default user group
            $admin = UserGroup::create(['name'=>'admin', 'display_name'=>'Admin', 'company_id'=>$company->id]);
            $operator = UserGroup::create(['name'=>'operator', 'display_name'=>'Operator', 'company_id'=>$company->id]);
            $query = DB::table('actions');
            $action_count = $query->count();
            $query->chunkById($action_count, function ($actions) use($admin, $operator, $company) {
                foreach($actions as $action){
                    DB::table('user_group_actions')->updateOrInsert(
                        ['user_group_id'=>$admin->id,'action_id'=>$action->id,'company_id'=>$company->id],
                        ['user_group_id'=>$admin->id,'action_id'=>$action->id,'company_id'=>$company->id]
                    );
                    if(in_array($action->name, ['index','view']) || in_array($action->group,['reports']) ||
                    (in_array($action->name, ['journals', 'vouchers', 'contacts']) && in_array($action->name, ['edit', 'create', 'delete']))
                    ){
                        DB::table('user_group_actions')->updateOrInsert(
                            ['user_group_id'=>$operator->id,'action_id'=>$action->id,'company_id'=>$company->id],
                            ['user_group_id'=>$operator->id,'action_id'=>$action->id,'company_id'=>$company->id]
                        );
                    }
                }
            });
            //setup data
            //insert data journal type, numbering format
            \App\Numbering::createDefault($company->id);
            // JournalType::createDefault($company->id);
            // Account::createDefaultAccount($company->id, $user->id);
            // Account::createDefaultAccountType($company->id, $user->id);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
        $this->setCompanyActive(encode($company->id));
        return redirect()->route('home');
    }

    public function setCompanyActive($company_id){
        $owner = Auth::user();
        $company_id = decode($company_id);
        $activeCompany = Company::findOrFail($company_id);
        Company::where('owner_id', $owner->id)->update(['is_active'=>false]);
        $activeCompany->is_active = true;
        $activeCompany->save();
        // return new CompanyResource($activeCompany);
    }

    public function update(Request $request){
        
        $user = Auth::user();
        $company = $user->activeCompany();
        validate($request->all(), [
            'name' => 'required|max:128',
            'company_type_id' => 'required|max:128',
            'email'=>'email|max:64',
            'website'=>'max:64',
            'phone'=>'max:64',
            'fax'=>'max:64',
            'address'=>'max:256',
        ]);
        $company->name = isset($request->name)?$request->name:$company->name;
        $company->company_type_id = isset($request->company_type_id)?$request->company_type_id:$company->company_type_id;
        $company->address = isset($request->address)?$request->address:$company->address;
        $company->shipping_address = isset($request->shipping_address)?$request->shipping_address:$company->shipping_address;
        $company->phone = isset($request->phone)?$request->phone:$company->phone;
        $company->fax = isset($request->fax)?$request->fax:$company->fax;
        $company->email = isset($request->email)?$request->email:$company->email;
        $company->website = isset($request->website)?$request->website:$company->website;
        $company->tax_no = isset($request->tax_no)?$request->tax_no:$company->tax_no;
        $company->save();
        return new CompanyResource($company);
    }

    

    public function upload(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();
        
        $filename = Str::slug($company->name.' '.date('Y m d H i s').' '.time(),'-');
        $filename = upload_file('logo', $filename, 'public/company');        
        $company->logo = $filename;
        $company->save();
        return new CompanyResource($company);
    }
    
    public function getCompanyTypes(){
        $types = CompanyType::all();
        return CompanyTypeResource::collection($types);
    }

    public function delete(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $password = Auth::user()->password;
        if(!(\Hash::check($request->password, $password))) {
            return redirect()->back()->with('error', 'Wrong password');
        }
        $company->delete();
        return redirect()->route('companies.index', company('id'))->with('success', 'Company has been deleted.');
    }
    public function confirmDelete($id)
    {
        $user = Auth::user();
        $companies = Company::where('owner_id', $user->id)
        ->orderBy('is_active', 'desc')
        ->orderBy('name')
        ->get();
        $company = Company::findOrFail($id);
        return view('company.form_delete', compact('company', 'companies'));
    }
    public function import()
    {
        $user = Auth::user();
        $companies = Company::where('owner_id', $user->id)
        ->orderBy('is_active', 'desc')
        ->orderBy('name')
        ->get();
        $company = $user->activeCompany();
        return view('company.import', compact('company', 'companies'));
    }
    public function export()
    {
        $user = Auth::user();
        $companies = Company::where('owner_id', $user->id)
        ->orderBy('is_active', 'desc')
        ->orderBy('name')
        ->get();
        $company = $user->activeCompany();
        return view('company.export', compact('company', 'companies'));
    }
    public function transfer()
    {
        $user = Auth::user();
        $companies = Company::where('owner_id', $user->id)
        ->orderBy('is_active', 'desc')
        ->orderBy('name')
        ->get();
        $company = $user->activeCompany();
        return view('company.transfer', compact('company', 'companies'));
    }
    public function convert()
    {
        $user = Auth::user();
        $companies = Company::where('owner_id', $user->id)
        ->orderBy('is_active', 'desc')
        ->orderBy('name')
        ->get();
        $company = $user->activeCompany();
        return view('company.convert', compact('company', 'companies'));
    }
}