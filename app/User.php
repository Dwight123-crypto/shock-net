<?php

namespace App;
use DB;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // 'name', 'email', 'password',
        'name', 'email', 'password', 'lastname', 'firstname', 'middlename', 'dob', 'gender', 'address', 'country', 'phone', 'avatar', 'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function canAccess($permission_key){

        if(empty($GLOBALS['permissions'])){

            $permissions = DB::table('roles_permissions as rp')
            ->select("rp.permission_key")
            ->where('rp.roles_id', $this->role_id)
            ->where('rp.permission_allowed', 1)
            ->get();

            $permission_array = [];

            foreach($permissions as $p){
                $permission_array[] = $p->permission_key;
            }

            $GLOBALS['permissions'] = $permission_array;

        }
        return in_array($permission_key, $GLOBALS['permissions']); // This will return true or false
    }
}
