<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Item;

class ItemCRUDController extends Controller
{

    public function __construct(){
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
        if(!auth()->user()->canAccess('ItemCRUDController.index')){
            return view('permission-denied');
        }
        // User role permission end here


        $items = Item::orderBy('id','DESC')->paginate(5);
        //return view('ItemCRUD.index',compact('items'))
        return view('product-list.index',compact('items'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ItemCRUDController.add')){
            return view('permission-denied');
        }
        // User role permission end here


        return view('product-list.create');
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
            'product_name' => 'required',
            'price' => 'required',
        ]);

        Item::create($request->all());
        //return redirect()->route('itemCRUD.index')
        return redirect()->route('product-list.index')
                        ->with('success','Product created successfully');
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
        if(!auth()->user()->canAccess('ItemCRUDController.show')){
            return view('permission-denied');
        }
        // User role permission end here


        $item = Item::find($id);
        return view('product-list.show',compact('item'));
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
        if(!auth()->user()->canAccess('ItemCRUDController.edit')){
            return view('permission-denied');
        }
        // User role permission end here


        $item = Item::find($id);
        return view('product-list.edit',compact('item'));
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
            'product_name' => 'required',
            'price' => 'required',
        ]);

        Item::find($id)->update($request->all());
        return redirect()->route('product-list.index')
                        ->with('success','Product updated successfully');
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
        if(!auth()->user()->canAccess('ItemCRUDController.delete')){
            return view('permission-denied');
        }
        // User role permission end here


        Item::find($id)->delete();
        return redirect()->route('product-list.index')
                        ->with('success','Product deleted successfully');
    }
}