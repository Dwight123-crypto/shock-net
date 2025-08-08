<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Vendor;
use App\SupplierInvoice;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;

class VendorController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {
        // User role permission start here
        if(!auth()->user()->canAccess('VendorController.index')){
            return view('permission-denied');
        }
        // User role permission end here


        $vendor = Vendor::orderBy('company_name', 'ASC')
        ->orderBy('first_name', 'ASC')
        ->paginate(15);
        if($vendor) {
            foreach($vendor as &$item) {
                $item->fullname = ($item->individual)
                    ? trim("{$item->first_name} {$item->middle_name} {$item->last_name}")
                    : trim("{$item->company_name}");
            }
        }

        return view('vendors.index', compact('vendor'));
    }

    public function searchVendors(Request $request){
        
        // User role permission start here
        if(!auth()->user()->canAccess('VendorController.index')){
            return view('permission-denied');
        }
        // User role permission end here

        $search = $request->search;

        if(!empty($search)){
            $vendor = Vendor::from('vendors as ven')
            ->select('ven.*')
            ->orWhere('ven.first_name', 'LIKE', "%$search%")
            ->orWhere('ven.middle_name', 'LIKE', "%$search%")
            ->orWhere('ven.last_name', 'LIKE', "%$search%")
            ->orWhere('ven.company_name', 'LIKE', "%$search%")
            ->orderBy('ven.first_name', 'ASC')
            ->orderBy('ven.company_name', 'ASC')
            ->paginate(15);

            if($vendor) {
                foreach($vendor as &$item) {
                    $item->fullname = ($item->individual)
                        ? trim("{$item->first_name} {$item->middle_name} {$item->last_name}")
                        : trim("{$item->company_name}");
                }
            }
        }else{}
        // dd($vendor);

        return view('vendors.index', compact('vendor'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        // User role permission start here
        if(!auth()->user()->canAccess('VendorController.add')){
            return view('permission-denied');
        }
        // User role permission end here


        return view('vendors.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(Request $request)
    {
        // $this->validate($request, ['last_name' => '128', 'first_name' => '128', 'middle_name' => '128', 'tin' => '64', 'branch_code' => '32', 'phone_number' => '32', 'fax' => '32', ]);

        Vendor::create($request->all());

        Session::flash('flash_message', 'Vendor added!');

        return redirect('vendors');
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
        if(!auth()->user()->canAccess('VendorController.show')){
            return view('permission-denied');
        }
        // User role permission end here


        $vendor = Vendor::findOrFail($id);

        return view('vendors.show', compact('vendor'));
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
        if(!auth()->user()->canAccess('VendorController.edit')){
            return view('permission-denied');
        }
        // User role permission end here


        $vendor = Vendor::findOrFail($id);

        return view('vendors.edit', compact('vendor'));
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
        // $this->validate($request, ['last_name' => '128', 'first_name' => '128', 'middle_name' => '128', 'tin' => '64', 'branch_code' => '32', 'phone_number' => '32', 'fax' => '32', ]);

        $vendor = Vendor::findOrFail($id);
        $vendor->update($request->all());

        Session::flash('flash_message', 'Vendor updated!');

        return redirect('vendors');
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
        if(!auth()->user()->canAccess('VendorController.delete')){
            return view('permission-denied');
        }
        // User role permission end here


        Vendor::destroy($id);

        Session::flash('flash_message', 'Vendor deleted!');

        return redirect('vendors');
    }
    
    public function getSupplierInvoices(Request $request)
    {
        return (new SupplierInvoice)->getInvoicesByVendorId($request->vendor_id);
    }
}
