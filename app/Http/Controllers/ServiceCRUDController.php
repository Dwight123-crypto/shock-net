<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Service;
use Session;

class ServiceCRUDController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ServiceCRUDController.index')){
            return view('permission-denied');
        }
        // User role permission end here


        $services = Service::orderBy('id', 'DESC')->paginate(5);
        
        return view('service-list.index',compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ServiceCRUDController.add')){
            return view('permission-denied');
        }
        // User role permission end here


        return view('service-list.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'rate' => 'required',
        ]);

        Service::create($request->all());
        
        Session::flash('flash_message', 'Service added!');
        return redirect('service-list');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ServiceCRUDController.show')){
            return view('permission-denied');
        }
        // User role permission end here


        $service = Service::find($id);
        return view('service-list.show',compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ServiceCRUDController.edit')){
            return view('permission-denied');
        }
        // User role permission end here


        $service = Service::find($id);
        return view('service-list.edit',compact('service'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'rate' => 'required',
        ]);

        Service::find($id)->update($request->all());

        Session::flash('flash_message', 'Service updated!');
        return redirect('service-list');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ServiceCRUDController.delete')){
            return view('permission-denied');
        }
        // User role permission end here


        Service::find($id)->delete();
        
        Session::flash('flash_message', 'Service deleted!');
        return redirect('service-list');
    }
}