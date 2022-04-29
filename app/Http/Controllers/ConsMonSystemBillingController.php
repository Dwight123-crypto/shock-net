<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Customer;
use App\CMSProject;
use App\CMSBilling;

use Carbon\Carbon;
use DateTime;
use DB;

class ConsMonSystemBillingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemBillingController.index')){
            return view('permission-denied');
        }
        // User role permission end here

        $search_billing_invoice = $request->search_billing_invoice;
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        $cms_billing = CMSBilling::from('c_m_s_billings as billing')
        ->leftjoin('customers as cust','billing.customer_id','=','cust.id')
        ->leftjoin('c_m_s_projects as project','billing.project_id','=','project.id')
        ->select(DB::raw("billing.*, project.*, cust.*, billing.id as id, billing.date as date, billing.status as status"))
        ->orderby('billing.date', 'desc')
        ->orderby('project.name', 'asc')
        ->paginate(10);

        $total_billing = CMSBilling::from('c_m_s_billings as billing')
        ->leftjoin('customers as cust','billing.customer_id','=','cust.id')
        ->leftjoin('c_m_s_projects as project','billing.project_id','=','project.id')
        ->select(DB::raw("billing.amount"))
        ->sum('billing.amount');

        $billing_amount = 0;
        foreach($cms_billing as $key => $item){
            $billing_amount = $billing_amount + $item->amount;
        }
        
        return view('construction-monitoring-billing.index', compact('cms_billing','search_billing_invoice','date_from','date_to','billing_amount','total_billing'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    public function createInvoice($id)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemBillingController.add')){
            return view('permission-denied');
        }
        // User role permission end here

        $cm_project = CMSProject::from('c_m_s_projects as project')
        ->where('id', $id)
        ->first();

        $billing_invoice = CMSBilling::latest()->first();

        return view('construction-monitoring-billing/create-invoice', compact('cm_project','billing_invoice'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        if(empty($request->customer_id)){
            return back()->with('warning','No customer for this project found!!!');
        }
        
        $amount = $request->amount;

        $amount = str_replace( ',', '', $amount );

        $cmsbilling = new CMSBilling();
        $cmsbilling->billing_invoice_no = $request->invoice_no;   
        $cmsbilling->customer_id = $request->customer_id;
        $cmsbilling->project_id = $request->id;
        $cmsbilling->amount = $amount; 
        $cmsbilling->date = $request->date;
        $cmsbilling->status = 'Unpaid';
        $cmsbilling->save();

        return redirect('construction-monitoring-billing')
            ->with('success', 'Project billing invoice is successfully save.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        if(!auth()->user()->canAccess('ConsMonSystemBillingController.edit')){
            return view('permission-denied');
        }
        // User role permission end here

        $cm_billing = CMSProject::from('c_m_s_billings as billing')
        ->leftjoin('customers as cust','billing.customer_id','=','cust.id')
        ->leftjoin('c_m_s_projects as project','billing.project_id','=','project.id')
        ->select(DB::raw("project.*, billing.*, cust.id, billing.id as id"))
        ->where('billing.id', $id)
        ->first();

        return view('construction-monitoring-billing.edit', compact('cm_billing'));
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
        if(empty($request->customer_id)){
            return back()->with('warning','No customer for this project found!!!');
        }
        
        $amount = $request->amount;

        $amount = str_replace( ',', '', $amount );

        $cmsbilling = CMSBilling::find($id); 
        $cmsbilling->amount = $amount; 
        $cmsbilling->date = $request->date;
        $cmsbilling->save();

        return redirect('construction-monitoring-billing')
            ->with('success', 'Project billing invoice is successfully updated.');
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
        if(!auth()->user()->canAccess('ConsMonSystemBillingController.edit')){
            return view('permission-denied');
        }
        // User role permission end here
        
        CMSBilling::destroy($id);
        
        return back()->with('success','Billing invoice is successfully deleted.');
    }

    public function getCustomers(Request $request)
    {
        $s = $request->term;
        
        $customer = Customer::select('*', DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name)) as `name`'))
            ->where('individual', '=', 1)
            ->where(function($query) use ($s){
                $query->where('first_name', 'LIKE', "$s%")
                    ->orWhere('middle_name', 'LIKE', "$s%")
                    ->orWhere('last_name', 'LIKE', "$s%");
            });
            
        $customer = Customer::select('*', 'company_name as name')
            ->where('individual', '=', 0)
            ->where('company_name', 'LIKE', "$s%")
            ->unionAll($customer);
            
        $customers = $customer->get();
        
        $response = [];
        foreach($customers as $c){
            $response[] = [ 'id' => $c->id, 'label' => $c->name, 'value' => $c->name ];
        }
        
        return $response;
    }

    function findCustomerOnly(Request $request)
    {
        if(!empty($request->customer_id)) {
            $customer = Customer::select('*', DB::raw("TRIM(IF(individual, CONCAT(first_name, ' ', middle_name, ' ', last_name), company_name)) as full_name"))
                ->find($request->customer_id);
            
            if(!empty($request->inc_open) && $customer) {
                $customer->open_invoices = (new OpenInvoice)->getInvoicesByCustomerId($customer->id);
            }
            
            return $customer? $customer: [];
        }
        
        $s = $request->cust_name;
        
        if(!$s) return [];
        
        $customer = Customer::select('*', DB::raw('CONCAT(`first_name`," ",`middle_name`," ",`last_name`) as full_name'))
            ->where('individual', '=', 1)
            ->where(DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name))'), 'LIKE', "%$s%")
            ->first();
        
        if(!$customer) {
            $customer = Customer::select('*', 'company_name as full_name')
                ->where('individual', '=', 0)
                ->where('company_name', 'LIKE', "%$s%")
                ->first();
        }
        
        return $customer? $customer: [];
    }

    public function search(Request $request){

        $search_billing_invoice = $request->search_billing_invoice;
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        if(!empty($search_billing_invoice) && empty($date_from)){

            $cms_billing = CMSBilling::from('c_m_s_billings as billing')
            ->leftjoin('customers as cust','billing.customer_id','=','cust.id')
            ->leftjoin('c_m_s_projects as project','billing.project_id','=','project.id')
            ->select(DB::raw("billing.*, project.*, cust.*, billing.id as id, billing.date as date, billing.status as status"))
            ->where('billing.status', $search_billing_invoice)
            ->orWhere(DB::raw('TRIM(CONCAT(cust.first_name, " ", 
            cust.middle_name, " ", 
            cust.last_name, " ", 
            cust.company_name, " ", 
            project.name, " ", 
            project.type, " ", 
            billing.billing_invoice_no))'
            ), 'LIKE', "%$search_billing_invoice%")
            ->orderby('billing.date', 'desc')
            ->orderby('project.name', 'asc')
            ->paginate(10);

        }elseif(!empty($search_billing_invoice) && !empty($date_from)){

            $cms_billing = CMSBilling::from('c_m_s_billings as billing')
            ->leftjoin('customers as cust','billing.customer_id','=','cust.id')
            ->leftjoin('c_m_s_projects as project','billing.project_id','=','project.id')
            ->select(DB::raw("billing.*, project.*, cust.*, billing.id as id, billing.date as date, billing.status as status"))
            ->whereDate('billing.date', '>=', $date_from)
            ->whereDate('billing.date', '<=', $date_to)
            ->where('billing.status', $search_billing_invoice)
            ->orWhere(DB::raw(
            'TRIM(CONCAT(cust.first_name, " ", 
            cust.middle_name, " ", 
            cust.last_name, " ", 
            cust.company_name, " ", 
            project.name, " ", 
            project.type, " ", 
            billing.billing_invoice_no))'
            ), 'LIKE', "%$search_billing_invoice%")
            ->orderby('billing.date', 'desc')
            ->orderby('project.name', 'asc')
            ->paginate(10);

        }else{

            $cms_billing = CMSBilling::from('c_m_s_billings as billing')
            ->leftjoin('customers as cust','billing.customer_id','=','cust.id')
            ->leftjoin('c_m_s_projects as project','billing.project_id','=','project.id')
            ->select(DB::raw("billing.*, project.*, cust.*, billing.id as id, billing.date as date, billing.status as status"))
            ->whereDate('billing.date', '>=', $date_from)
            ->whereDate('billing.date', '<=', $date_to)
            ->orderby('billing.date', 'desc')
            ->orderby('project.name', 'asc')
            ->paginate(10);
        }

        return view('construction-monitoring-billing.index', compact('cms_billing','search_billing_invoice','date_from','date_to'));
    }

    // function findProjectAutoSuggest(Request $request){

    //     $s = $request->term;
    //     $project = CMSProject::select('id', 'name')
    //         ->where('name', 'LIKE', "%$s%")
    //         ->orderBy('name','ASC');

    //     $projects = $project->get();
    //     $response = [];
    //     foreach($project as $c){

    //         $response[] = [ 'id' => $c->id, 'label' => $c->name, 'value' => $c->name ];
    //     }
    //     return $response;
    // }

    // function findProject(Request $request){
        
    //     $s = $request->s;
       
    //     // $total_added_qty = "(SELECT SUM(added_qty) FROM inventories inv where prod.id = inv.pro_id) as total_added_qty";
    //     // $sold_qty  = "(SELECT SUM(qty) FROM p_o_s_soldstocks pos_s where prod.id = pos_s.product_id) as sold_qty";
    
    //     if(!empty($request->prod_id)) {

    //         $searchproject  = CMSProject::from('c_m_s_projects as proj')
    //         ->select(DB::raw("proj.*, proj.id as proj_id")) //$sold_qty, $total_added_qty"))
    //         ->where('proj.id', $request->proj_id)
    //         ->first();

    //         // $searchproduct = Product::find($request->prod_id);
    //         if($searchproject) 
    //             $searchproject->name = trim( "{$searchproject->name}");

    //         return $searchproject? $searchproject: [];
    //     }

    //     if(!$s) return [];

    //     // $searchproduct = Product::where('name', 'LIKE', "$s%")
    //     $searchproject  = CMSProject::from('c_m_s_projects as proj')
    //     ->select(DB::raw("proj.*, proj.id as proj_id")) // $sold_qty, $total_added_qty"))
    //     ->where('proj.name', 'LIKE', "$s%")
    //     ->first();
        
    //     return $searchproject? $searchproject: [];
    // }
}
