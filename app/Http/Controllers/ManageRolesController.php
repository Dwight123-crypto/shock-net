<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\ManageRoles;
use App\User;
use DB;

class ManageRolesController extends Controller
{
    public function index(Request $request){

        // User role permission start here
        $user = auth()->user();
        if($user->role_id != 1){
            return view('permission-denied');
        }
        // User role permission end here

        $roles = ManageRoles::orderby('role_name', 'ASC')
        ->paginate(10);

        return view('manage-roles.index', compact('roles'));
    }

    public function create(){

        // User role permission start here
        $user = auth()->user();
        if($user->role_id != 1){
            return view('permission-denied');
        }
        // User role permission end here

        return view('manage-roles.create');
    }

    public function store(Request $request){

        $check_roles = ManageRoles::where('role_name', $request->role_name)->first();

        if(empty($check_roles)){

            ManageRoles::create($request->all());

            return redirect()->route('manage-roles.index')
            ->with('success','New role is successfully created...');

        }else{

            return back()->with('warning', 'Role name is already exist!!!');
        }
    }

    public function edit($id){

        // User role permission start here
        $user = auth()->user();
        if($user->role_id != 1){
            return view('permission-denied');
        }
        // User role permission end here

        $role = ManageRoles::find($id);
        return view('manage-roles.edit',compact('role'));
    }

    public function update(Request $request, $id){

        $this->validate($request, [
            'role_name'  => 'required',
        ]);

        $check_roles = ManageRoles::where('role_name', $request->role_name)->first();
        
        if(empty($check_roles)){

            ManageRoles::find($id)->update($request->all());

            return redirect()->route('manage-roles.index')
                ->with('success','Role updated successfully');
        }else{

            return back()->with('warning', 'Role name is already exist!!!');
        }
    }
}
