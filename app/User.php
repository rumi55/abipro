<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'is_owner', 'owner_id', 'is_active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function activeCompany(){
        if($this->is_owner){
            return Company::where('owner_id', $this->id)->where('is_active', true)->first();
        }else{
            return CompanyUser::where('user_id', $this->id)->first()->userGroup->company;
        }
    }

    public function userGroup(){
        if(!$this->is_owner){
            $company = $this->activeCompany();
            return CompanyUser::where('user_id', $this->id)
            ->where('company_id', $company->id)->first();
        }
    }
    /**
     * Company owner
     */
    public function isSuper(){
        return $this->is_owner;
    }
    /**
     * 
     */
    public function hasAction($group, $action){
        $action = \App\Action::where('group', $group)->where('name', $action)->first();
        if($action==null || $this->is_owner){
            return true;
        }
        $userGroup = $this->userGroup();
        $user_group_id = $userGroup!=null?$userGroup->user_group_id:0;
        return \DB::table('user_group_actions')->where('action_id', $action->id)->where('user_group_id', $user_group_id)->exists();
    }
}
