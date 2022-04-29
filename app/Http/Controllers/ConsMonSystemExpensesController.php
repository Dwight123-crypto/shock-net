<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Vendor;
use App\CMSExpenses;
use App\CMSProject;

use Carbon\Carbon;
use DateTime;
use DB;

class ConsMonSystemExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemExpensesController.index')){
            return view('permission-denied');
        }
        // User role permission end here

        $search_expenses_name_types = $request->search_expenses_name_types;

        $total_amount  = "(SELECT SUM(amount) FROM c_m_s_expenses cms_e where cms_p.id = cms_e.project_id) as total_amount";

        $expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
        ->leftjoin('c_m_s_projects as cms_p', 'cms_p.id', '=', 'cms_e.project_id')
        ->select(DB::raw("cms_p.*, cms_e.*, cms_e.id as id, $total_amount"))
        ->where('cms_e.expenses_type', 'project expenses')
        ->groupBy('cms_p.id')
        ->orderby('cms_p.name', 'asc')
        ->orderby('cms_p.type', 'asc')
        ->paginate(10);

        $overall_expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
        ->leftjoin('c_m_s_projects as cms_p', 'cms_p.id', '=', 'cms_e.project_id')
        ->select(DB::raw("cms_p.*, cms_e.*, cms_e.id as id, $total_amount"))
        ->where('cms_e.expenses_type', 'project expenses')
        ->sum('cms_e.amount');

        $total_expenses = 0;
        foreach($expenses as $key => $item){
            $total_expenses = $total_expenses + $item->total_amount;
        }

        return view('construction-monitoring-expenses.index', compact('expenses','search_expenses_name_types','total_expenses','overall_expenses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemExpensesController.add')){
            return view('permission-denied');
        }
        // User role permission end here

        // This will generate the latest entry_no from database
        $cms_expenses = CMSExpenses::latest()
        ->where('invoice_remarks', '')
        ->first();

        $cms_projects = CMSProject::where('status', 'Ongoing')
        ->orderby('name','asc')->get();

        return view('construction-monitoring-expenses.create',compact('cms_expenses','cms_projects'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(empty($request->vendor_id)){
            return back()->with('warning','Expenses cannot be save without vendors/suppliers!!!');
        }
        
        if($request->expenses_type == 'admin expenses'){
            $project_id = '0';
        }else{
            $project_id = $request->project_id;
        }

        $amount = $request->amount;

        $amount = str_replace( ',', '', $amount );

        $cm_expenses = new CMSExpenses();
        $cm_expenses->vendor_id = $request->vendor_id;
        $cm_expenses->project_id = $project_id;
        $cm_expenses->invoice_no = $request->invoice_no;
        $cm_expenses->invoice_remarks = $request->invoice_remarks;
        $cm_expenses->terms = $request->terms;
        $cm_expenses->period = $request->period;
        $cm_expenses->description = $request->description;
        $cm_expenses->amount = $amount;
        $cm_expenses->date = $request->date;
        $cm_expenses->expenses_type = $request->expenses_type;
        $cm_expenses->save();

        return back()->with('success','Expenses is successfully saved.');
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
        if(!auth()->user()->canAccess('ConsMonSystemExpensesController.edit')){
            return view('permission-denied');
        }
        // User role permission end here

        $cms_projects = CMSProject::where('status', 'Ongoing')
        ->orderby('name','asc')->get();

        $cms_expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
        ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
        ->leftjoin('c_m_s_projects as cms_p', 'cms_p.id', '=', 'cms_e.project_id')
        ->select(DB::raw("ven.*, cms_e.*, cms_p.*, cms_e.id as id, cms_e.date as date, cms_p.id as project_id"))
        ->where('cms_e.id', $id)
        ->first();

        return view('construction-monitoring-expenses.edit',compact('cms_expenses','cms_projects'));
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
        if($request->expenses_type == 'admin expenses'){
            $project_id = 0;
        }else{
            $project_id = $request->project_id;
        }

        $amount = $request->amount;

        $amount = str_replace( ',', '', $amount );

        $cm_expenses = CMSExpenses::find($id);
        $cm_expenses->vendor_id = $request->vendor_id;
        $cm_expenses->project_id = $project_id;
        $cm_expenses->invoice_no = $request->invoice_no;
        $cm_expenses->terms = $request->terms;
        $cm_expenses->period = $request->period;
        $cm_expenses->description = $request->description;
        $cm_expenses->amount = $amount;
        $cm_expenses->date = $request->date;
        $cm_expenses->expenses_type = $request->expenses_type;
        $cm_expenses->save();

        if($project_id == 0){
            return redirect('construction-monitoring-expenses/admin-expenses')
            ->with('success', 'Expenses is successfully updated.');
        }

        return back()->with('success','Expenses is successfully updated.');
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
        if(!auth()->user()->canAccess('ConsMonSystemExpensesController.delete')){
            return view('permission-denied');
        }
        // User role permission end here

        CMSExpenses::destroy($id);
        
        return back()->with('success','Expenses is successfully deleted.');
    }

    public function deleteAdminExpenses($id){

        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemExpensesController.delete')){
            return view('permission-denied');
        }
        // User role permission end here

        CMSExpenses::destroy($id);
        
        return back()->with('success','Admin expenses is successfully deleted.');
    }

    function findVendor(Request $request)
    {
        if(!empty($request->vendor_id)) {
            $vendor = Vendor::select('*', DB::raw("TRIM(IF(individual, CONCAT(first_name, ' ', middle_name, ' ', last_name), company_name)) as name"))
                ->find($request->vendor_id);
            
            return $vendor? $vendor: [];
        }
        
        $s = $request->s;
        
        if(!$s) return [];
        
        $vendor = Vendor::select('*', DB::raw('CONCAT(`first_name`," ",`middle_name`," ",`last_name`) as name'))
            ->where('individual', '=', 1)
            ->where(DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name))'), 'LIKE', "%$s%")
            ->first();
        
        if(!$vendor) {
            $vendor = Vendor::select('*', 'company_name as name')
                ->where('individual', '=', 0)
                ->where('company_name', 'LIKE', "%$s%")
                ->first();
        }
        
        return $vendor? $vendor: [];
    }
    
    function findVendors(Request $request)
    {
        $s = $request->term;
        
        if(!$s) return [];
        
        $vendors = Vendor::select('*', DB::raw("TRIM(IF(individual, CONCAT(first_name, ' ', middle_name, ' ', last_name), company_name)) as name"))
            ->where(function($query) use ($s){
                $query->where('first_name', 'LIKE', "$s%")
                    ->orWhere('middle_name', 'LIKE', "$s%")
                    ->orWhere('last_name', 'LIKE', "$s%");
            })
            ->orWhere('company_name', 'LIKE', "$s%")
            ->get();
        
        if(!$vendors) return [];
        
        $response = [];
        foreach($vendors as $v){
            $response[] = [ 'id' => $v->id, 'label' => $v->name, 'value' => $v->name ];
        }
        
        return $response;
    }

    public function searchExpenses(Request $request){

        $search_expenses_name_types = $request->search_expenses_name_types;

        $total_amount  = "(SELECT SUM(amount) FROM c_m_s_expenses cms_e where cms_p.id = cms_e.project_id) as total_amount";

        $expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
        ->leftjoin('c_m_s_projects as cms_p', 'cms_p.id', '=', 'cms_e.project_id')
        ->select(DB::raw("cms_p.*, cms_e.*, cms_e.id as id, $total_amount"))
        ->where('cms_e.expenses_type', 'project expenses')
        ->where('cms_p.type', 'LIKE', "%$search_expenses_name_types%")
        ->orWhere('cms_p.name', 'LIKE', "%$search_expenses_name_types%")
        ->groupBy('cms_p.id')
        ->orderby('cms_p.name', 'asc')
        ->orderby('cms_p.type', 'asc')
        ->paginate(10);

        $overall_expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
        ->leftjoin('c_m_s_projects as cms_p', 'cms_p.id', '=', 'cms_e.project_id')
        ->select(DB::raw("cms_p.*, cms_e.*, cms_e.id as id, $total_amount"))
        ->where('cms_e.expenses_type', 'project expenses')
        ->where('cms_p.type', 'LIKE', "%$search_expenses_name_types%")
        ->orWhere('cms_p.name', 'LIKE', "%$search_expenses_name_types%")
        ->sum('cms_e.amount');

        $total_expenses = 0;
        foreach($expenses as $key => $item){
            $total_expenses = $total_expenses + $item->total_amount;
        }

        return view('construction-monitoring-expenses.index', compact('expenses','search_expenses_name_types','overall_expenses','total_expenses'));
    }

    public function details($id){

        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemExpensesController.view_details')){
            return view('permission-denied');
        }
        // User role permission end here

        $search_expenses_details = '';

        $cms_projects = CMSProject::where('id', $id)->first();

        $details = CMSExpenses::from('c_m_s_expenses as cms_e')
        ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
        ->leftjoin('c_m_s_projects as cms_p', 'cms_p.id', '=', 'cms_e.project_id')
        ->select(DB::raw("ven.*, cms_e.*, cms_p.name, cms_p.type, cms_e.id as id, cms_e.date as date"))
        ->where('cms_p.id', $id)
        ->orderby('cms_e.date', 'desc')
        ->orderby('ven.company_name', 'asc')
        ->orderby('ven.last_name', 'asc')
        ->orderby('cms_p.name', 'asc')
        ->paginate(10);

        $total_details_expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
        ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
        ->leftjoin('c_m_s_projects as cms_p', 'cms_p.id', '=', 'cms_e.project_id')
        ->select(DB::raw("ven.*, cms_e.*, cms_p.name, cms_p.type, cms_e.id as id, cms_e.date as date"))
        ->where('cms_p.id', $id)
        ->sum('cms_e.amount');

        $details_expenses = 0;
        foreach($details as $key => $item){
            $details_expenses = $details_expenses + $item->amount;
        }
        
        return view('construction-monitoring-expenses.details',compact('details','search_expenses_details','cms_projects','details_expenses','total_details_expenses'));
    }

    public function searchDetails(Request $request){

        $search_expenses_details = $request->search_expenses_details;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $id = $request->project_id;

        $cms_projects = CMSProject::where('id', $id)->first();

        if(!empty($search_expenses_details) && empty($date_from)){

            $details = CMSExpenses::from('c_m_s_expenses as cms_e')
            ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
            ->leftjoin('c_m_s_projects as cms_p', 'cms_p.id', '=', 'cms_e.project_id')
            ->select(DB::raw("ven.*, cms_e.*, cms_p.name, cms_p.type, cms_e.id as id, cms_e.date as date"))
            ->Where(DB::raw('TRIM(CONCAT(ven.first_name, " ", ven.middle_name, " ", ven.last_name, " ", ven.company_name, " ", cms_e.description, " ", cms_e.invoice_no))'), 'LIKE', "%$search_expenses_details%")
            ->where('cms_p.id', $id)
            ->orderby('cms_e.date', 'desc')
            ->orderby('ven.company_name', 'asc')
            ->orderby('ven.last_name', 'asc')
            ->orderby('cms_p.name', 'asc')
            ->paginate(10);

            $total_details_expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
            ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
            ->leftjoin('c_m_s_projects as cms_p', 'cms_p.id', '=', 'cms_e.project_id')
            ->select(DB::raw("ven.*, cms_e.*, cms_p.name, cms_p.type, cms_e.id as id, cms_e.date as date"))
            ->Where(DB::raw('TRIM(CONCAT(ven.first_name, " ", ven.middle_name, " ", ven.last_name, " ", ven.company_name, " ", cms_e.description, " ", cms_e.invoice_no))'), 'LIKE', "%$search_expenses_details%")
            ->where('cms_p.id', $id)
            ->sum('cms_e.amount');

        }elseif(!empty($search_expenses_details) && !empty($date_from)){

            $details = CMSExpenses::from('c_m_s_expenses as cms_e')
            ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
            ->leftjoin('c_m_s_projects as cms_p', 'cms_p.id', '=', 'cms_e.project_id')
            ->select(DB::raw("ven.*, cms_e.*, cms_p.name, cms_p.type, cms_e.id as id, cms_e.date as date"))
            ->whereDate('cms_e.date', '>=', $date_from)
            ->whereDate('cms_e.date', '<=', $date_to)
            ->Where(DB::raw('TRIM(CONCAT(ven.first_name, " ", ven.middle_name, " ", ven.last_name, " ", ven.company_name, " ", cms_e.description, " ", cms_e.invoice_no))'), 'LIKE', "%$search_expenses_details%")
            ->where('cms_p.id', $id)
            ->orderby('cms_e.date', 'desc')
            ->orderby('ven.company_name', 'asc')
            ->orderby('ven.last_name', 'asc')
            ->orderby('cms_p.name', 'asc')
            ->paginate(10);

            $total_details_expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
            ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
            ->leftjoin('c_m_s_projects as cms_p', 'cms_p.id', '=', 'cms_e.project_id')
            ->select(DB::raw("ven.*, cms_e.*, cms_p.name, cms_p.type, cms_e.id as id, cms_e.date as date"))
            ->whereDate('cms_e.date', '>=', $date_from)
            ->whereDate('cms_e.date', '<=', $date_to)
            ->Where(DB::raw('TRIM(CONCAT(ven.first_name, " ", ven.middle_name, " ", ven.last_name, " ", ven.company_name, " ", cms_e.description, " ", cms_e.invoice_no))'), 'LIKE', "%$search_expenses_details%")
            ->where('cms_p.id', $id)
            ->sum('cms_e.amount');

        }else{

            $details = CMSExpenses::from('c_m_s_expenses as cms_e')
            ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
            ->leftjoin('c_m_s_projects as cms_p', 'cms_p.id', '=', 'cms_e.project_id')
            ->select(DB::raw("ven.*, cms_e.*, cms_p.name, cms_p.type, cms_e.id as id, cms_e.date as date"))
            ->whereDate('cms_e.date', '>=', $date_from)
            ->whereDate('cms_e.date', '<=', $date_to)
            ->where('cms_p.id', $id)
            ->orderby('cms_e.date', 'desc')
            ->orderby('ven.company_name', 'asc')
            ->orderby('ven.last_name', 'asc')
            ->orderby('cms_p.name', 'asc')
            ->paginate(10);

            $total_details_expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
            ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
            ->leftjoin('c_m_s_projects as cms_p', 'cms_p.id', '=', 'cms_e.project_id')
            ->select(DB::raw("ven.*, cms_e.*, cms_p.name, cms_p.type, cms_e.id as id, cms_e.date as date"))
            ->whereDate('cms_e.date', '>=', $date_from)
            ->whereDate('cms_e.date', '<=', $date_to)
            ->where('cms_p.id', $id)
            ->sum('cms_e.amount');
        }

        $details_expenses = 0;
        foreach($details as $key => $item){
            $details_expenses = $details_expenses + $item->amount;
        }

        return view('construction-monitoring-expenses.details',compact('details','search_expenses_details','cms_projects','details_expenses','total_details_expenses'));
    }

    public function adminExpenses(Request $request){

        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemExpensesController.index')){
            return view('permission-denied');
        }
        // User role permission end here

        $search_vendor_description = $request->search_vendor_description;
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        $expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
        ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
        ->select(DB::raw("ven.*, cms_e.*, cms_e.id as id"))
        ->where('cms_e.expenses_type', 'admin expenses')
        ->orderby('cms_e.date', 'desc')
        ->orderby('ven.last_name', 'asc')
        ->orderby('ven.last_name', 'asc')
        ->paginate(10);

        $overall_expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
        ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
        ->select(DB::raw("ven.*, cms_e.*, cms_e.id as id"))
        ->where('cms_e.expenses_type', 'admin expenses')
        ->sum('cms_e.amount');

        $admin_expenses = 0;
        foreach($expenses as $key => $item){
            $admin_expenses = $admin_expenses + $item->amount;
        }

        return view('construction-monitoring-expenses.admin-expenses', compact('expenses','search_vendor_description','date_from','date_to','admin_expenses','overall_expenses'));
    }

    public function searchAdminExpenses(Request $request){
        
        $search_vendor_description = $request->search_vendor_description;
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        if(!empty($search_vendor_description) && empty($date_from)){
        
            $expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
            ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
            ->select(DB::raw("ven.*, cms_e.*, cms_e.id as id"))
            ->where('cms_e.expenses_type', 'admin expenses')
            ->Where(DB::raw('TRIM(CONCAT(ven.first_name, " ", ven.middle_name, " ", ven.last_name, " ", ven.company_name, " ", cms_e.description, " ", cms_e.invoice_no))'), 'LIKE', "%$search_vendor_description%")
            ->orderby('cms_e.date', 'desc')
            ->orderby('ven.last_name', 'asc')
            ->orderby('ven.last_name', 'asc')
            ->paginate(10);

            $overall_expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
            ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
            ->select(DB::raw("ven.*, cms_e.*, cms_e.id as id"))
            ->where('cms_e.expenses_type', 'admin expenses')
            ->Where(DB::raw('TRIM(CONCAT(ven.first_name, " ", ven.middle_name, " ", ven.last_name, " ", ven.company_name, " ", cms_e.description, " ", cms_e.invoice_no))'), 'LIKE', "%$search_vendor_description%")
            ->sum('cms_e.amount');
        
        }elseif(!empty($search_vendor_description) && !empty($date_from)){
            
            $expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
            ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
            ->select(DB::raw("ven.*, cms_e.*, cms_e.id as id"))
            ->where('cms_e.expenses_type', 'admin expenses')
            ->whereDate('cms_e.date', '>=', $date_from)
            ->whereDate('cms_e.date', '<=', $date_to)
            ->Where(DB::raw('TRIM(CONCAT(ven.first_name, " ", ven.middle_name, " ", ven.last_name, " ", ven.company_name, " ", cms_e.description, " ", cms_e.invoice_no))'), 'LIKE', "%$search_vendor_description%")
            ->orderby('cms_e.date', 'desc')
            ->orderby('ven.last_name', 'asc')
            ->orderby('ven.last_name', 'asc')
            ->paginate(10);

            $overall_expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
            ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
            ->select(DB::raw("ven.*, cms_e.*, cms_e.id as id"))
            ->where('cms_e.expenses_type', 'admin expenses')
            ->whereDate('cms_e.date', '>=', $date_from)
            ->whereDate('cms_e.date', '<=', $date_to)
            ->Where(DB::raw('TRIM(CONCAT(ven.first_name, " ", ven.middle_name, " ", ven.last_name, " ", ven.company_name, " ", cms_e.description, " ", cms_e.invoice_no))'), 'LIKE', "%$search_vendor_description%")
            ->sum('cms_e.amount');

        }else{

            $expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
            ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
            ->select(DB::raw("ven.*, cms_e.*, cms_e.id as id"))
            ->where('cms_e.expenses_type', 'admin expenses')
            ->whereDate('cms_e.date', '>=', $date_from)
            ->whereDate('cms_e.date', '<=', $date_to)
            ->orderby('cms_e.date', 'desc')
            ->orderby('ven.last_name', 'asc')
            ->orderby('ven.last_name', 'asc')
            ->paginate(10);

            $overall_expenses = CMSExpenses::from('c_m_s_expenses as cms_e')
            ->leftjoin('vendors as ven', 'ven.id', '=', 'cms_e.vendor_id')
            ->select(DB::raw("ven.*, cms_e.*, cms_e.id as id"))
            ->where('cms_e.expenses_type', 'admin expenses')
            ->whereDate('cms_e.date', '>=', $date_from)
            ->whereDate('cms_e.date', '<=', $date_to)
            ->sum('cms_e.amount');
        }

        $admin_expenses = 0;
        foreach($expenses as $key => $item){
            $admin_expenses = $admin_expenses + $item->amount;
        }

        return view('construction-monitoring-expenses.admin-expenses', compact('expenses','search_vendor_description','date_from','date_to','admin_expenses','overall_expenses'));
    }
}
