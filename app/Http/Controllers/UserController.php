<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use Redirect;
use Auth;
use Image;
use App\ManageRoles;
use DB;

use App\Services\EmailService;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('auth', ['only' => 'create']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(Request $request)
    {
        // if ($request->user()->cannot('create-user')) {
           // abort(403, 'Unauthorized action.');
        // }
        // User role permission start here
        $user = auth()->user();
        if($user->role_id != 1){
            return view('permission-denied');
        }
        // User role permission end here

        $user = User::from('users as user')
        ->leftjoin('manage_roles as role', 'role.id', '=', 'user.role_id')
        ->Select(DB::raw("user.*, role.*, role.id as role_id, user.id as id"))
        ->orderby('name', 'ASC')
        ->paginate(15);
        // $overAlltotal  = Product::from('products as prod')
        // ->leftjoin('vendors', 'vendors.id', '=', 'prod.vendor_id')

        return view('user.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create(Request $request)
    {
        // User role permission start here
        $user = auth()->user();
        if($user->role_id != 1){
            return view('permission-denied');
        }
        // User role permission end here


        /* if ($request->user()->cannot('create-user')) {
           abort(403, 'Unauthorized action.');
        } */

        $roles = ManageRoles::orderby('role_name', 'ASC')->get();

        return view('user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(Request $request)
    {
        /* if ($request->user()->cannot('create-user')) {
           abort(403, 'Unauthorized action.');
        } */

        $user = User::whereEmail($request->email)->first();

        if ($user) {
            return Redirect::back()->withErrors( 'User with the same email already exist.' );
        }

        $data = $request->all();
        $data['password'] = bcrypt($this->getToken(8, Carbon::today()->timestamp));

        $user = User::create($data);

        $data = [
            'user'      => $user,
            'admin'     => \Auth::user(),
            'password'  => $this->getToken(8, Carbon::today()->timestamp)
        ];
        return redirect('user');

        // EmailService::sendWelcomeEmail($data, $user);

        // return redirect('user');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function show($id)
    {
        // User role permission start here
        $user = auth()->user();
        if($user->role_id != 1){
            return view('permission-denied');
        }
        // User role permission end here


        // if ($request->user()->cannot('create-user')) {
           // abort(403, 'Unauthorized action.');
        // }

        $user = User::findOrFail($id);

        return view('user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function edit($id)
    {
        // User role permission start here
        $user = auth()->user();
        if($user->role_id != 1){
            return view('permission-denied');
        }
        // User role permission end here


        $user = User::findOrFail($id);

        $roles = ManageRoles::orderby('role_name', 'ASC')->get();

        return view('user.edit', compact('user','roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function update($id, Request $request)
    {
        // print_r( $request->all() );
        // die;
        $user = User::findOrFail($id);
        $user->update(['name'=>$request->name, 'access_token'=>$request->access_token, 'role_id'=>$request->role_id]);

        return redirect('user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function destroy($id)
    {
        // User role permission start here
        $user = auth()->user();
        if($user->role_id != 1){
            return view('permission-denied');
        }
        // User role permission end here
        

        User::destroy($id);

        Session::flash('flash_message', 'User deleted!');

        return redirect('user');
    }

    private function getToken($length, $seed)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "0123456789";

        mt_srand($seed);

        for($i=0;$i<$length;$i++){
            $token .= $codeAlphabet[mt_rand(0,strlen($codeAlphabet)-1)];
        }
        return $token;
    }

    public function getAccessToken(Request $request)
    {
        return $this->getToken(15, time());
    }
    
    public function getProfile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }
    
    public function postProfile(Request $request)
    {
        $user = Auth::user();
        
        $rules = [ 'avatar' => 'max:3500', ];
        
        /* Add below rules only if one the these fields are filled in */
        $update_password = false;
        if( !empty( $request->password ) || !empty( $request->password_confirmation ) ) {
            $rules[ 'password' ] = 'required|same:password_confirmation';
            $update_password = true;
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()){
            return view('user.profile', compact('user'))
                ->withErrors($validator);
        }
        
        /* File */
        if($request->file('avatar')){
            ini_set('memory_limit','-1');
            
            $avatar = $request->file('avatar');
            
            $filename = strtolower( $avatar->getClientOriginalName() );
            $filename = time() . '-' . $filename;
            
            Image::make($avatar)->resize(150, 150)->save( public_path("uploads/images/{$filename}") );
            
            $user->avatar = $filename;
        }
        
        /* Update the user's password */
        if( $update_password ) {
            $user->password = bcrypt( $request->password );
        }
        
        $user->name = $request->name;
        $user->save();
        
        Session::flash('flash_message', 'Profile updated!');
        
        return view('user.profile', compact('user'));
    }
}
