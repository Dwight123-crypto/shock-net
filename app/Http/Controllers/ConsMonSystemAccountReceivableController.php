<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Customer;
use App\CMSProject;
use App\CMSBilling;
use App\CMSAccountReceivable;

use Carbon\Carbon;
use DateTime;
use DB;

class ConsMonSystemAccountReceivableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemAccountReceivableController.index')){
            return view('permission-denied');
        }
        // User role permission end here

        $search_ar = $request->search_ar;
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        $cms_ar = CMSAccountReceivable::from('c_m_s_account_receivables as ar')
        ->leftjoin('customers as cust','ar.customer_id','=','cust.id')
        ->leftjoin('c_m_s_billings as billing','ar.billing_id','=','billing.id')
        ->leftjoin('c_m_s_projects as project','ar.project_id','=','project.id')
        ->select(DB::raw("ar.*, billing.*, project.*, cust.*, ar.id as id, ar.date as date, ar.amount as amount"))
        ->orderby('ar.date', 'desc')
        ->orderby('cust.last_name', 'asc')
        ->orderby('cust.company_name', 'asc')
        ->orderby('ar.or_number', 'asc')
        ->paginate(10);

        $total = 0;
        foreach($cms_ar as $key => $item){
            $total = $total + $item->amount;
        }

        $overall_total = CMSAccountReceivable::sum('amount');

        return view('construction-monitoring-ar.index', compact('cms_ar','search_ar','date_from','date_to','total','overall_total'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function createAR($id)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemAccountReceivableController.add')){
            return view('permission-denied');
        }
        // User role permission end here

        $cm_billing = CMSBilling::from('c_m_s_billings as billing')
        ->leftjoin('c_m_s_projects as project','billing.project_id','=','project.id')
        ->select(DB::raw("billing.*, project.*, billing.id as id"))
        ->where('billing.id', $id)
        ->first();

        $cms_ar = CMSAccountReceivable::latest()->first();

        return view('construction-monitoring-ar/create-ar', compact('cm_billing','cms_ar'));
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

        $cmsAR = new CMSAccountReceivable();
        $cmsAR->or_number = $request->or_number;
        $cmsAR->billing_id = $request->id;   
        $cmsAR->customer_id = $request->customer_id;
        $cmsAR->project_id = $request->project_id;
        $cmsAR->amount = $amount; 
        $cmsAR->date = $request->date;
        $cmsAR->save();

        $cmsbilling = CMSBilling::find($request->id); 
        $cmsbilling->status = 'Paid';
        $cmsbilling->save();

        return redirect('construction-monitoring-ar')
            ->with('success', 'New account receivable is successfully save.');
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
        if(!auth()->user()->canAccess('ConsMonSystemAccountReceivableController.edit')){
            return view('permission-denied');
        }
        // User role permission end here

        $cms_ar_edit = CMSAccountReceivable::from('c_m_s_account_receivables as ar')
        ->leftjoin('customers as cust','ar.customer_id','=','cust.id')
        ->leftjoin('c_m_s_billings as billing','ar.billing_id','=','billing.id')
        ->leftjoin('c_m_s_projects as project','ar.project_id','=','project.id')
        ->select(DB::raw("ar.*, billing.*, project.*, cust.*, ar.id as id, ar.date as date, ar.amount as amount"))
        ->where('ar.id', $id)
        ->first();

        return view('construction-monitoring-ar.edit', compact('cms_ar_edit'));
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

        $cmsAR = CMSAccountReceivable::find($id);
        $cmsAR->amount = $amount;
        $cmsAR->date = $request->date;
        $cmsAR->save();

        return redirect('construction-monitoring-ar')
            ->with('success', 'Account receivable is successfully update');
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
        if(!auth()->user()->canAccess('ConsMonSystemAccountReceivableController.delete')){
            return view('permission-denied');
        }
        // User role permission end here

        CMSAccountReceivable::destroy($id);
        
        return back()->with('success','Account receivable is successfully deleted.');
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

        $search_ar = $request->search_ar;
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        if(!empty($search_ar) && empty($date_from)){

            $cms_ar = CMSAccountReceivable::from('c_m_s_account_receivables as ar')
            ->leftjoin('customers as cust','ar.customer_id','=','cust.id')
            ->leftjoin('c_m_s_billings as billing','ar.billing_id','=','billing.id')
            ->leftjoin('c_m_s_projects as project','ar.project_id','=','project.id')
            ->select(DB::raw("ar.*, billing.*, project.*, cust.*, ar.id as id, ar.date as date, ar.amount as amount"))
            ->where(DB::raw('TRIM(CONCAT(cust.first_name, " ", 
            cust.middle_name, " ", 
            cust.last_name, " ", 
            cust.company_name, " ", 
            project.name, " ", 
            project.type, " ", 
            ar.or_number))'
            ), 'LIKE', "%$search_ar%")
            ->orderby('ar.date', 'desc')
            ->orderby('cust.last_name', 'asc')
            ->orderby('cust.company_name', 'asc')
            ->orderby('ar.or_number', 'asc')
            ->paginate(10);

            $overall_total = CMSAccountReceivable::from('c_m_s_account_receivables as ar')
            ->leftjoin('customers as cust','ar.customer_id','=','cust.id')
            ->leftjoin('c_m_s_billings as billing','ar.billing_id','=','billing.id')
            ->leftjoin('c_m_s_projects as project','ar.project_id','=','project.id')
            ->select(DB::raw("ar.amount"))
            ->where(DB::raw('TRIM(CONCAT(cust.first_name, " ", 
            cust.middle_name, " ", 
            cust.last_name, " ", 
            cust.company_name, " ", 
            project.name, " ", 
            project.type, " ", 
            ar.or_number))'
            ), 'LIKE', "%$search_ar%")
            ->sum('ar.amount');
        
        }elseif(!empty($search_ar) && !empty($date_from)){

            $cms_ar = CMSAccountReceivable::from('c_m_s_account_receivables as ar')
            ->leftjoin('customers as cust','ar.customer_id','=','cust.id')
            ->leftjoin('c_m_s_billings as billing','ar.billing_id','=','billing.id')
            ->leftjoin('c_m_s_projects as project','ar.project_id','=','project.id')
            ->select(DB::raw("ar.*, billing.*, project.*, cust.*, ar.id as id, ar.date as date, ar.amount as amount"))
            ->whereDate('ar.date', '>=', $date_from)
            ->whereDate('ar.date', '<=', $date_to)
            ->where(DB::raw('TRIM(CONCAT(cust.first_name, " ", 
            cust.middle_name, " ", 
            cust.last_name, " ", 
            cust.company_name, " ", 
            project.name, " ", 
            project.type, " ", 
            ar.or_number))'
            ), 'LIKE', "%$search_ar%")
            ->orderby('ar.date', 'desc')
            ->orderby('cust.last_name', 'asc')
            ->orderby('cust.company_name', 'asc')
            ->orderby('ar.or_number', 'asc')
            ->paginate(10);

            $overall_total = CMSAccountReceivable::from('c_m_s_account_receivables as ar')
            ->leftjoin('customers as cust','ar.customer_id','=','cust.id')
            ->leftjoin('c_m_s_billings as billing','ar.billing_id','=','billing.id')
            ->leftjoin('c_m_s_projects as project','ar.project_id','=','project.id')
            ->select(DB::raw("ar.amount"))
            ->whereDate('ar.date', '>=', $date_from)
            ->whereDate('ar.date', '<=', $date_to)
            ->where(DB::raw('TRIM(CONCAT(cust.first_name, " ", 
            cust.middle_name, " ", 
            cust.last_name, " ", 
            cust.company_name, " ", 
            project.name, " ", 
            project.type, " ", 
            ar.or_number))'
            ), 'LIKE', "%$search_ar%")
            ->sum('ar.amount');

        }else{

            $cms_ar = CMSAccountReceivable::from('c_m_s_account_receivables as ar')
            ->leftjoin('customers as cust','ar.customer_id','=','cust.id')
            ->leftjoin('c_m_s_billings as billing','ar.billing_id','=','billing.id')
            ->leftjoin('c_m_s_projects as project','ar.project_id','=','project.id')
            ->select(DB::raw("ar.*, billing.*, project.*, cust.*, ar.id as id, ar.date as date, ar.amount as amount"))
            ->whereDate('ar.date', '>=', $date_from)
            ->whereDate('ar.date', '<=', $date_to)
            ->orderby('ar.date', 'desc')
            ->orderby('cust.last_name', 'asc')
            ->orderby('cust.company_name', 'asc')
            ->orderby('ar.or_number', 'asc')
            ->paginate(10);

            $overall_total = CMSAccountReceivable::from('c_m_s_account_receivables as ar')
            ->leftjoin('customers as cust','ar.customer_id','=','cust.id')
            ->leftjoin('c_m_s_billings as billing','ar.billing_id','=','billing.id')
            ->leftjoin('c_m_s_projects as project','ar.project_id','=','project.id')
            ->select(DB::raw("ar.amount"))
            ->whereDate('ar.date', '>=', $date_from)
            ->whereDate('ar.date', '<=', $date_to)
            ->sum('ar.amount');
        }

        $total = 0;
        foreach($cms_ar as $key => $item){
            $total = $total + $item->amount;
        }

        return view('construction-monitoring-ar.index', compact('cms_ar','search_ar','date_from','date_to','total','overall_total'));
    }
}
