<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Vendor;
use App\Inventory;
use DateTime;
use DB;

class ProductController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request){

        // User role permission start here
        if(!auth()->user()->canAccess('ProductController.index')){
            return view('permission-denied');
        }
        // User role permission end here


        $search_products = $request->search_products;
        $inventory_status = $request->inventory_status;

        $products = DB::table('products')
        ->leftjoin('vendors', 'vendors.id', '=', 'products.vendor_id')
        ->select('products.*', 'vendors.*', 'products.id as id')
        ->orderBy('products.name', 'ASC')
        ->paginate(10);

        // $products = Product::orderBy('id','DESC')->paginate(10);
        return view('product.index',compact('products','inventory_status','search_products'))
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
        if(!auth()->user()->canAccess('ProductController.add')){
            return view('permission-denied');
        }
        // User role permission end here


        $vendors = DB::table('vendors')
        ->where('vendors_status','=',' ')
        ->orWhere('vendors_status','=','Active')
        ->orderBy('first_name', 'ASC')
        ->orderBy('company_name', 'ASC')
        ->get();
        return view('product.create',compact('vendors'));
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
            'name'  => 'required',
            'price' => 'required',
            'cost_price' => 'required',
            'vendor_id' => 'required',
            'inventory_status' => 'required',
        ]);

        Product::create($request->all());
        
        $currDate = date("Y-m-d");
        $latestRec = Product::latest()->first();
        $latestId = $latestRec->id;
        $addToInventory = new Inventory();
        $addToInventory->pro_id = $latestId;
        $addToInventory->added_qty = $request->added_qty;
        $addToInventory->date = $currDate;
        $addToInventory->save();

        return redirect()
            ->route('product.index')
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
        if(!auth()->user()->canAccess('ProductController.show')){
            return view('permission-denied');
        }
        // User role permission end here


        // $product = Product::find($id);
        $product = DB::table('products')
        ->leftjoin('vendors', 'vendors.id', '=', 'products.vendor_id')
        ->select('products.*', 'vendors.*', 'products.id as id')
        ->where('products.id', $id)
        ->first();        
    
        return view('product.show',compact('product'));
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
        if(!auth()->user()->canAccess('ProductController.edit')){
            return view('permission-denied');
        }
        // User role permission end here


        $product = Product::find($id);

        $vendors = DB::table('vendors')
        ->where('vendors_status','=',' ')
        ->orWhere('vendors_status','=','Active')
        ->orderBy('first_name', 'ASC')
        ->orderBy('company_name', 'ASC')
        ->get();
        return view('product.edit',compact('product','vendors'));
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
            'name'  => 'required',
            'price' => 'required',
            'cost_price' => 'required',
            'vendor_id' => 'required',
            'inventory_status' => 'required',
        ]);
        Product::find($id)->update($request->all());

        return redirect()
            ->route('product.index')
            ->with('success','Product updated successfully');
    }

    public function productDetails(Request $request, $id){

        // User role permission start here
        if(!auth()->user()->canAccess('ProductController.view_details')){
            return view('permission-denied');
        }
        // User role permission end here


        $productID = Product::find($id); // finding product ID
        $productName = Product::find($id); // finding product Name

        $vendorName = DB::table('products')
        ->leftjoin('vendors', 'vendors.id', '=', 'products.vendor_id')
        ->select('products.*', 'vendors.*', 'vendors.id as v_id')
        ->where('products.id', '=', $id)
        ->first();

        $details = DB::table('products')
        ->leftjoin('inventories', 'inventories.pro_id', '=', 'products.id')
        ->leftjoin('vendors', 'vendors.id', '=', 'products.vendor_id')
        ->select('products.*', 'inventories.*', 'vendors.*', 'inventories.id as inv_id')
        ->where('products.id', '=', $id)
        ->orderBy('inventories.date', 'DESC')
        ->paginate(10);

        return view('product.details',compact('details', 'productID', 'productName', 'vendorName'))
        ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function addStock(Request $request, $id){

        // User role permission start here
        if(!auth()->user()->canAccess('ProductController.add')){
            return view('permission-denied');
        }
        // User role permission end here


        $product_ID = Product::find($id); // finding product ID
        return view('product.addstock', compact('product_ID'));
    }

    public function saveStock(Request $request){

        $date = $request->date;
        $date = date("Y-m-d", strtotime($date) );

        $addToInventory = new Inventory();
        $addToInventory->pro_id = $request->pro_id;
        $addToInventory->added_qty = $request->added_qty;
        $addToInventory->date = $date;
        $addToInventory->save();
        
        return redirect()->back()->with('success','Stock is added successfully');
    }

    public function updateQty(Request $request){

        foreach ($request->invrow as $rowdata) {

            $addedQty = $rowdata['added_qty'];
            $inv_id = $rowdata['inv_id'];

            if(!empty($addedQty)){
                Inventory::where('id',$inv_id)->update(
                    array(
                        'added_qty' => $rowdata['added_qty']
                    )
                );
            }
        }
        return redirect()->back()->with('success','Stock quantity is successfully updated');
    }

    public function searchProduct(Request $request){

        $search_products = $request->search_products;
        $inventory_status = $request->inventory_status;

        if($inventory_status == 'All'){
            
            $products = DB::table('products')
            ->leftjoin('vendors', 'vendors.id', '=', 'products.vendor_id')
            ->select('products.*', 'vendors.*', 'products.id as id')
            // ->where('products.inventory_status', '=', 'Active')
            // ->where('products.inventory_status', '=', 'Inactive')
            // ->orWhere('products.inventory_status', '=', " ")
            ->orderBy('products.name', 'ASC')
            ->paginate(10);
    
            return view('product.index',compact('products','inventory_status','search_products'))
                ->with('i', ($request->input('page', 1) - 1) * 5);

        }elseif($inventory_status){

            $products = DB::table('products')
            ->leftjoin('vendors', 'vendors.id', '=', 'products.vendor_id')
            ->select('products.*', 'vendors.*', 'products.id as id')
            ->where('products.inventory_status', '=', $inventory_status)
            ->orderBy('products.name', 'ASC')
            ->paginate(10);
    
            return view('product.index',compact('products','inventory_status','search_products'))
                ->with('i', ($request->input('page', 1) - 1) * 5);
        }

        if(!empty($search_products)){
            $products = DB::table('products')
            ->leftjoin('vendors', 'vendors.id', '=', 'products.vendor_id')
            ->select('products.*', 'vendors.*', 'products.id as id')
            ->where('products.name', 'LIKE', "%$search_products%")
            ->orderBy('products.name', 'ASC')
            ->paginate(10);
    
            return view('product.index',compact('products','inventory_status','search_products'))
                ->with('i', ($request->input('page', 1) - 1) * 5);
        }
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
        if(!auth()->user()->canAccess('ProductController.delete')){
            return view('permission-denied');
        }
        // User role permission end here


        Product::find($id)->delete();
        
        return redirect()
            ->route('product.index')
            ->with('success','Product deleted successfully');
    }

    public function detailsDestroy($id=null){

        // User role permission start here
        if(!auth()->user()->canAccess('ProductController.delete')){
            return view('permission-denied');
        }
        // User role permission end here


        Inventory::where('id', '=', $id)->delete();
        return redirect()->back()->with('success','Stock detail is successfully deleted');
    }

    public function expiredProduct(Request $request){

        // User role permission start here
        if(!auth()->user()->canAccess('ProductController.view_details')){
            return view('permission-denied');
        }
        // User role permission end here


        $total_soldstock  = "(SELECT SUM(qty) FROM p_o_s_soldstocks poss where poss.product_id = prod.id) as total_soldstock";
        $total_stock  = "(SELECT SUM(added_qty) FROM inventories inv where inv.pro_id = prod.id) as total_stock";

        $expired_products = DB::table('products as prod')
        ->leftjoin('p_o_s_soldstocks as poss', 'poss.product_id','=','prod.id')
        ->leftjoin('vendors', 'vendors.id', '=', 'prod.vendor_id')
        ->select(DB::raw("vendors.* ,poss.*, prod.*, prod.id as prod_id, poss.id as id, $total_soldstock, $total_stock"))
        ->where('prod.expiration_date', '<=', date('Y-m-d'))
        ->groupBy('prod.id')
        ->orderBy('prod.name', 'ASC')
        ->paginate(10);
        // $products = Product::orderBy('id','DESC')->paginate(10);
        return view('product.expired-products',compact('expired_products'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }
}