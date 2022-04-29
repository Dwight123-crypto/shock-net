<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\CMSExpenses;
use App\CMSProject;

use Carbon\Carbon;
use DateTime;
use DB;

class ConsMonSystemReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemReportController.index')){
            return view('permission-denied');
        }
        // User role permission end here

        $search_name_type = $request->search_name_type;
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        $total_expenses  = "(SELECT SUM(amount) FROM c_m_s_expenses cms_e where cms_p.id = cms_e.project_id) as total_expenses";
        $total_ar_amount  = "(SELECT SUM(amount) FROM c_m_s_account_receivables ar where cms_p.id = ar.project_id) as total_ar_amount";
        $total_aw_amount = "(SELECT SUM(amount) FROM c_m_s_additional_works aw where cms_p.id = aw.project_id) as total_aw_amount";

        $cm_report = CMSProject::from('c_m_s_projects as cms_p')
        ->select(DB::raw("cms_p.*, cms_p.id as id, cms_p.date as cp_date, $total_expenses, $total_ar_amount, $total_aw_amount"))
        ->groupby('cms_p.id')
        ->orderby('cms_p.date', 'desc')
        ->orderby('cms_p.name', 'asc')
        ->paginate(10);

        $total_cost = 0;
        $total_aw_cost = 0;
        $total_downpayment = 0;
        $total_collected_payment = 0;
        $total_cp_expenses = 0;
        $total_gross_income = 0;
        $total_balance = 0;

        foreach($cm_report as $key => $item){
            $total_cost = $total_cost + $item->cost;
            $total_aw_cost = $total_aw_cost + $item->total_aw_amount;
            $total_downpayment = $total_downpayment + $item->downpayment;
            $total_collected_payment = $total_collected_payment + ($item->downpayment + $item->total_ar_amount); // plus the total amount from account receivable billing
            $total_cp_expenses = $total_cp_expenses + $item->total_expenses;
            $total_gross_income = $total_gross_income + (($item->cost + $item->total_aw_amount) - $item->total_expenses);
            $total_balance = $total_balance + (($item->cost + $item->total_aw_amount) - ($item->downpayment + $item->total_ar_amount)); // plus the total amount from account receivable billing
        }
        
        $overall_cm_report = CMSProject::from('c_m_s_projects as cms_p')
        ->select(DB::raw("cms_p.*, cms_p.id as id, cms_p.date as cp_date, $total_expenses, $total_ar_amount, $total_aw_amount"))
        ->groupby('cms_p.id')
        ->get();

        $overall_total_cost = 0;
        $overall_total_aw_cost = 0;
        $overall_total_downpayment = 0;
        $overall_total_collected_payment = 0;
        $overall_total_cp_expenses = 0;
        $overall_total_gross_income = 0;
        $overall_total_balance = 0;

        foreach($overall_cm_report as $key => $item){
            $overall_total_cost = $overall_total_cost + $item->cost;
            $overall_total_aw_cost = $overall_total_aw_cost + $item->total_aw_amount;
            $overall_total_downpayment = $overall_total_downpayment + $item->downpayment;
            $overall_total_collected_payment = $overall_total_collected_payment + ($item->downpayment + $item->total_ar_amount); // plus the total amount from account receivable billing
            $overall_total_cp_expenses = $overall_total_cp_expenses + $item->total_expenses;
            $overall_total_gross_income = $overall_total_gross_income + (($item->cost + $item->total_aw_amount) - $item->total_expenses);
            $overall_total_balance = $overall_total_balance + (($item->cost + $item->total_aw_amount) - ($item->downpayment + $item->total_ar_amount)); // plus the total amount from account receivable billing
        }

        $all_project_cost = CMSProject::sum('cost');

        $all_project_expenses = CMSExpenses::where('expenses_type', 'project expenses')
        ->sum('amount');

        $all_gross_income = $all_project_cost - $all_project_expenses;

        $all_admin_expenses = CMSExpenses::where('expenses_type', 'admin expenses')
        ->sum('amount');



        return view('construction-monitoring-report.index', compact(
            'cm_report',
            'search_name_type',
            'date_from',
            'date_to',
            'total_cost',
            'total_aw_cost',
            'total_downpayment',
            'total_collected_payment',
            'total_cp_expenses',
            'total_gross_income',
            'total_balance',
            'overall_total_cost',
            'overall_total_aw_cost',
            'overall_total_downpayment',
            'overall_total_collected_payment',
            'overall_total_cp_expenses',
            'overall_total_gross_income',
            'overall_total_balance',
            'all_gross_income',
            'all_admin_expenses'
        ));
    }

    public function search(Request $request){

        $search_name_type = $request->search_name_type;
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        $total_expenses  = "(SELECT SUM(amount) FROM c_m_s_expenses cms_e where cms_p.id = cms_e.project_id) as total_expenses";
        $total_ar_amount  = "(SELECT SUM(amount) FROM c_m_s_account_receivables ar where cms_p.id = ar.project_id) as total_ar_amount";
        $total_aw_amount = "(SELECT SUM(amount) FROM c_m_s_additional_works aw where cms_p.id = aw.project_id) as total_aw_amount";

        if(!empty($search_name_type)){

            $cm_report = CMSProject::from('c_m_s_projects as cms_p')
            ->select(DB::raw("cms_p.*, cms_p.id as id, cms_p.date as cp_date, $total_expenses, $total_ar_amount, $total_aw_amount"))
            ->Where(DB::raw('TRIM(CONCAT(cms_p.name, " ", cms_p.type))'), 'LIKE', "%$search_name_type%")
            ->groupby('cms_p.id')
            ->orderby('cms_p.date', 'desc')
            ->orderby('cms_p.name', 'asc')
            ->paginate(10);

            $overall_cm_report = CMSProject::from('c_m_s_projects as cms_p')
            ->select(DB::raw("cms_p.*, cms_p.id as id, cms_p.date as cp_date, $total_expenses, $total_ar_amount, $total_aw_amount"))
            ->Where(DB::raw('TRIM(CONCAT(cms_p.name, " ", cms_p.type))'), 'LIKE', "%$search_name_type%")
            ->groupby('cms_p.id')
            ->get();
        
        }elseif(!empty($search_name_type) && !empty($date_from)){

            $cm_report = CMSProject::from('c_m_s_projects as cms_p')
            ->select(DB::raw("cms_p.*, cms_p.id as id, cms_p.date as cp_date, $total_expenses, $total_ar_amount, $total_aw_amount"))
            ->whereDate('cms_p.date', '>=', $date_from)
            ->whereDate('cms_p.date', '<=', $date_to)
            ->Where(DB::raw('TRIM(CONCAT(cms_p.name, " ", cms_p.type))'), 'LIKE', "%$search_name_type%")
            ->groupby('cms_p.id')
            ->orderby('cms_p.date', 'desc')
            ->orderby('cms_p.name', 'asc')
            ->paginate(10);

            $overall_cm_report = CMSProject::from('c_m_s_projects as cms_p')
            ->select(DB::raw("cms_p.*, cms_p.id as id, cms_p.date as cp_date, $total_expenses, $total_ar_amount, $total_aw_amount"))
            ->whereDate('cms_p.date', '>=', $date_from)
            ->whereDate('cms_p.date', '<=', $date_to)
            ->Where(DB::raw('TRIM(CONCAT(cms_p.name, " ", cms_p.type))'), 'LIKE', "%$search_name_type%")
            ->groupby('cms_p.id')
            ->get();

        }else{

            $cm_report = CMSProject::from('c_m_s_projects as cms_p')
            ->select(DB::raw("cms_p.*, cms_p.id as id, cms_p.date as cp_date, $total_expenses, $total_ar_amount, $total_aw_amount"))
            ->whereDate('cms_p.date', '>=', $date_from)
            ->whereDate('cms_p.date', '<=', $date_to)
            ->groupby('cms_p.id')
            ->orderby('cms_p.date', 'desc')
            ->orderby('cms_p.name', 'asc')
            ->paginate(10);

            $overall_cm_report = CMSProject::from('c_m_s_projects as cms_p')
            ->select(DB::raw("cms_p.*, cms_p.id as id, cms_p.date as cp_date, $total_expenses, $total_ar_amount, $total_aw_amount"))
            ->whereDate('cms_p.date', '>=', $date_from)
            ->whereDate('cms_p.date', '<=', $date_to)
            ->groupby('cms_p.id')
            ->get();
        }

        $total_cost = 0;
        $total_aw_cost = 0;
        $total_downpayment = 0;
        $total_collected_payment = 0;
        $total_cp_expenses = 0;
        $total_gross_income = 0;
        $total_balance = 0;

        foreach($cm_report as $key => $item){
            $total_cost = $total_cost + $item->cost;
            $total_aw_cost = $total_aw_cost + $item->total_aw_amount;
            $total_downpayment = $total_downpayment + $item->downpayment;
            $total_collected_payment = $total_collected_payment + ($item->downpayment + $item->total_ar_amount); // plus the total amount from account receivable billing
            $total_cp_expenses = $total_cp_expenses + $item->total_expenses;
            $total_gross_income = $total_gross_income + (($item->cost + $item->total_aw_amount) - $item->total_expenses);
            $total_balance = $total_balance + (($item->cost + $item->total_aw_amount) - ($item->downpayment + $item->total_ar_amount)); // plus the total amount from account receivable billing
        }

        $overall_total_cost = 0;
        $overall_total_aw_cost = 0;
        $overall_total_downpayment = 0;
        $overall_total_collected_payment = 0;
        $overall_total_cp_expenses = 0;
        $overall_total_gross_income = 0;
        $overall_total_balance = 0;

        foreach($overall_cm_report as $key => $item){
            $overall_total_cost = $overall_total_cost + $item->cost;
            $overall_total_aw_cost = $overall_total_aw_cost + $item->total_aw_amount;
            $overall_total_downpayment = $overall_total_downpayment + $item->downpayment;
            $overall_total_collected_payment = $overall_total_collected_payment + ($item->downpayment + $item->total_ar_amount); // plus the total amount from account receivable billing
            $overall_total_cp_expenses = $overall_total_cp_expenses + $item->total_expenses;
            $overall_total_gross_income = $overall_total_gross_income + (($item->cost + $item->total_aw_amount) - $item->total_expenses);
            $overall_total_balance = $overall_total_balance + (($item->cost + $item->total_aw_amount) - ($item->downpayment + $item->total_ar_amount)); // plus the total amount from account receivable billing
        }
        
        $all_project_cost = CMSProject::sum('cost');

        $all_project_expenses = CMSExpenses::where('expenses_type', 'project expenses')
        ->sum('amount');

        $all_gross_income = $all_project_cost - $all_project_expenses;

        $all_admin_expenses = CMSExpenses::where('expenses_type', 'admin expenses')
        ->sum('amount');

        return view('construction-monitoring-report.index', compact(
            'cm_report',
            'search_name_type',
            'date_from',
            'date_to',
            'total_cost',
            'total_aw_cost',
            'total_downpayment',
            'total_collected_payment',
            'total_cp_expenses',
            'total_gross_income',
            'total_balance',
            'overall_total_cost',
            'overall_total_aw_cost',
            'overall_total_downpayment',
            'overall_total_collected_payment',
            'overall_total_cp_expenses',
            'overall_total_gross_income',
            'overall_total_balance',
            'all_gross_income',
            'all_admin_expenses'
        ));
    }

    public function details($id){

        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemReportController.view_details')){
            return view('permission-denied');
        }
        // User role permission end here

        $project = CMSProject::where('id', $id)->first();

        $cms_project = CMSProject::from('c_m_s_projects as project')
        ->select(DB::raw("project.date, '' as invoice_no, '' as or_number, project.cost, project.downpayment, 0 as amount"))
        ->where('project.id', $id);

        $cms_expenses = CMSProject::from('c_m_s_expenses as expenses')
        ->select(DB::raw("expenses.date, expenses.invoice_no, '' as or_number, 0 as cost, 0 as downpayment, expenses.amount"))
        ->where('expenses.project_id', $id);

        $cms_aw = CMSProject::from('c_m_s_additional_works as aw')
        ->select(DB::raw("aw.date, '' as invoice_no, '' as or_number, 0 as cost, 0 as downpayment, aw.amount"))
        ->where('aw.project_id', $id);

        $cms_ar = CMSProject::from('c_m_s_account_receivables as ar')
        ->select(DB::raw("ar.date, '' as invoice_no, ar.or_number, 0 as cost, 0 as downpayment, ar.amount"))
        ->where('ar.project_id', $id)
        ->unionAll($cms_project)
        ->unionAll($cms_expenses)
        ->unionAll($cms_aw)
        ->orderby('date', 'asc')
        ->get();

        return view('construction-monitoring-report/details', compact('project','cms_ar'));
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
