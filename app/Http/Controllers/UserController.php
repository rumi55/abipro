<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyUserResource;
use App\Exceptions\ApiValidationException;
use App\User;
use App\UserGroup;
use App\Company;
use App\CompanyUser;
use App\JournalType;
use Auth;
use Validator;
use DB;
use Carbon\Carbon;


class UserController extends Controller
{
    public function getAll(Request $request){
        $owner = Auth::user();
        $company = $owner->activeCompany();
        $companyUser = $company->users;
        return CompanyUserResource::collection($companyUser);
    }
    
    public function get($id){
        $id = decode($id);

        return new CompanyUserResource(CompanyUser::findOrFail($id));
    }
    
    public function me(){
        return new UserResource(Auth::user());
    }
    public function index(){
        // $company = \Auth::user()->activeCompany();
        $data = dcru_dt('users', 'dtables');
        return view('user.index', $data);
    }
    public function create(){
        $company = Auth::user()->activeCompany();
        $mode = 'create';
        $user = new User;
        $groups = \App\UserGroup::where('company_id', $company->id)->get();
        
        return view('user.form', compact('user', 'groups', 'form', 'mode'));
    }
    public function edit($id){
        $company = Auth::user()->activeCompany();
        $mode = 'edit';
        $user = User::find($id);
        $groups = \App\UserGroup::where('company_id', $company->id)->get();
        return view('user.form', compact('user', 'groups', 'form', 'mode'));
    }
    /**
     * yang bisa menambahkan user adalah owner
     */
    public function save(Request $request){
        $owner = Auth::user();
        $company = $owner->activeCompany();
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'user_group_id' => 'required',
            'password' => 'required|min:8|string'
        ];

        $attr = [
            'name' => 'Nama',
            'email' => 'Email',
            'phone' => 'Telepon',
            'user_group_id' => 'Grup Pengguna',
            'password' => 'Password'
        ];
        $validator = \Validator::make($request->all(), $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try{
            DB::beginTransaction();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'is_owner'=> false,
                'owner_id'=> $owner->id,
                'password' => bcrypt($request->password)
            ]);
            $company_user = CompanyUser::updateOrCreate(
                ['user_id'=>$user->id, 'user_group_id'=>decode($request->user_group_id), 'company_id'=>$company->id],
                ['user_id'=>$user->id, 'user_group_id'=>decode($request->user_group_id), 'company_id'=>$company->id]    
            );

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
        add_log('users', 'create', '');
        return redirect()->route('users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }


    public function update(Request $request, $id){
        // $id = user('id');
        $owner = Auth::user();
        $company = $owner->activeCompany();
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,'.$id,
            'phone' => 'required|string|unique:users,phone,'.$id,
            'user_group_id' => 'required'
        ];
        
        $attr = [
            'name' => 'Nama',
            'email' => 'Email',
            'phone' => 'Telepon',
            'user_group_id' => 'Grup Pengguna'
        ];

        $validator = \Validator::make($request->all(), $rules)->setAttributeNames($attr);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = User::findOrFail($id);
        try{
            DB::beginTransaction();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();
            if(!empty($request->user_group_id)){
                $company_user = CompanyUser::where('user_id', $user->id)->where('company_id', $company->id)->first();
                $company_user->user_group_id = decode($request->user_group_id);
                $company_user->save();
            }
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
        add_log('users', 'update', '');
        return redirect()->route('users.index')->with('success', 'Informasi pengguna telah diubah.');
    }
    public function delete($id)
    {
        $id = decode($id);
        $company_user = CompanyUser::findOrFail($id);
        $user = User::findOrFail($company_user->user_id);
        $name = $user->name;
        $user->delete();
        add_log('users', 'delete', '');
        return redirect()->route('user_groups')->with('success', "Pengguna $name telah dihapus.");
    }

    public function register(Request $request){
        validate($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|min:8|string|confirmed'
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password)
        ]);
        $user->save();

        // return (new UserResource($user))
        //         ->response()
        //         ->setStatusCode(201);
        $credentials = ['email'=>$request->email, 'password'=>$request->password];
        if(!Auth::attempt($credentials)){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        
        $token->save();
        $result = [
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expired_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'user'=>new UserResource(Auth::user())
        ];
        return response()->json($result);
    }

    public function createCompany(Request $request){
        validate($request->all(), [
            'name' => 'required|string',
            'industry' => 'required|string',
            'accounting_period' => 'required',
        ]);
        $user = Auth::user();
        $data = array_merge($request->all(), ['owner_id'=>$user->id]);
        try{
            DB::beginTransaction();
            $company = Company::create($data);
            //create default user group
            UserGroup::create(['name'=>'admin', 'display_name'=>'Admin', 'company_id'=>$company->id]);
            UserGroup::create(['name'=>'operator', 'display_name'=>'Operator', 'company_id'=>$company->id]);
            
            //setup data
            //insert data journal type, numbering format
            JournalType::createDefault($company->id);
        
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
        return $this->setCompanyActive(encode($company->id));
    }

    public function setCompanyActive($company_id){
        $owner = Auth::user();
        $company_id = decode($company_id);
        $activeCompany = Company::findOrFail($company_id);
        Company::where('owner_id', $owner->id)->update(['is_active'=>false]);
        $activeCompany->is_active = true;
        $activeCompany->save();
        return new CompanyResource($activeCompany);
    }

    public function getCompanyUsers(){
        $owner = Auth::user();
        $company = $owner->activeCompany();
        return CompanyUserResource::collection($company->users);
    }

    public function view($id){
        $user = \App\User::findOrFail($id);
        return view('user.view', compact('user'));
    }
    public function profile(){
        $user = \Auth::user();
        return view('user.profile', compact('user'));
    }
    public function profileEdit(){
        $user = \Auth::user();
        return view('user.form_profile', compact('user'));
    }
    public function profileUpdate(Request $request){
        $user = Auth::user();
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,'.$user->id,
            'phone' => 'required|string|unique:users,phone,'.$user->id,
            'photo' => 'image|max:256'
        ];
        
        $attr = [
            'name' => 'Nama',
            'email' => 'Email',
            'phone' => 'Telepon',
            'photo' => 'Gambar',
        ];

        $validator = \Validator::make($request->all(), $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $filename = \Str::slug($request->name.' '.date('Y m d H i s').' '.time(),'-');
        $filename = upload_file('photo', $filename, 'public/user');        
        $old_photo = '';
        if(!empty($filename)){
            $old_photo = $user->photo;
            $user->photo = $filename;
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();
        if(!empty($old_photo)){
            \Storage::delete($old_photo);    
        }
        return redirect()->route('users.profile')->with('success', 'Profil pengguna telah diubah.');
    }
    
    public function action(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();
        $group=$request->get('group');
        $roles = UserGroup::where('company_id', $company->id)->get();
        $action_groups = DB::table('actions')->select('group', 'display_group')
        ->distinct()->get();
        
        $action_list = DB::table('actions');
        if(!empty($group)){
            $action_list = $action_list->where('group', $group);
        }
        $action_list = $action_list->get();
        $actions = array();
        foreach($action_list as $action){
            $actions[$action->group][] = $action;
        }
        $rhp = DB::table('user_group_actions')->get();
        $user_group_actions = array();
        foreach($rhp as $r){
            $user_group_actions[$r->user_group_id.'-'.$r->action_id] = $r;
        }
        $view = $request->ajax()?'user._action':'user.action';

        return view($view, compact('roles', 'user', 'actions', 'user_group_actions', 'action_groups', 'group'));
    }
    public function saveAction(Request $request){
        $actions = $request->input('actions');
        DB::transaction(function ()use($actions) {
            $company_id = company('id');
            $existings = DB::table('user_group_actions')->where('company_id', $company_id)
            ->selectRaw("concat(user_group_id,'-',action_id) as p")->pluck('p')->toArray();
            $existings = array_diff($existings, $actions);
            foreach($actions as $action){
                list($role_id, $action_id) = explode('-', $action);
                $data=array('user_group_id'=>$role_id, 'action_id'=>$action_id, 'company_id'=>$company_id);
                DB::table('user_group_actions')->updateOrInsert($data, $data);
            }
            foreach($existings as $action){
                list($role_id, $action_id) = explode('-', $action);
                $data=array('user_group_id'=>$role_id, 'action_id'=>$action_id);
                DB::table('user_group_actions')
                ->where('company_id', $company_id)
                ->where('user_group_id', $role_id)
                ->where('action_id', $action_id)->delete();
            }
        });
        return redirect(asset(route('users.actions', [], false)))->with('success', 'Hak akses pengguna berhasil disimpan.');
    }

    public function setLang(Request $request, $id){
        $user = Auth::user();
        $user->lang = $id;
        $user->save();
        return redirect()->back();

    }

}