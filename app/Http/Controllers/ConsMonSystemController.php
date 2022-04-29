<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Customer;
use App\CMSProject;
use App\CMSExpenses;
use App\CMSAccountReceivable;
use App\CMSBilling;
use App\CMSAdditionalWork;

use Carbon\Carbon;
use DateTime;
use DB;

class ConsMonSystemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemController.index')){
            return view('permission-denied');
        }
        // User role permission end here

        $current_date = date('Y-m-d');
        $current_month = date('m');
        $startDate = '';
        $endDate = '';

        // Non tax income for current date
        $total_downpayment = CMSProject::where('date', $current_date)
        ->sum('downpayment');
        $total_ar = CMSAccountReceivable::where('date', $current_date)
        ->sum('amount');
        $totalNonTaxIncomeToday = $total_downpayment + $total_ar;

        // No OR expenses for current date
        $totalNoORExpensesToday = CMSExpenses::where('date', $current_date)
        ->sum('amount');

        // Getting Gross Income
        $projectCost = CMSProject::sum('cost');
        $projAdditionalWorkCost = CMSAdditionalWork::sum('amount');
        $projectExpenses = CMSExpenses::where('expenses_type', 'project expenses')->sum('amount');
        $grossIncome = ($projectCost + $projAdditionalWorkCost) - $projectExpenses;

        // Getting Net Income
        $projectCost = CMSProject::sum('cost');
        $projAdditionalWorkCost = CMSAdditionalWork::sum('amount');
        $projectExpenses = CMSExpenses::where('expenses_type', 'project expenses')->sum('amount');
        $adminExpenses = CMSExpenses::where('expenses_type', 'admin expenses')->sum('amount');
        $netIncome = ($projectCost + $projAdditionalWorkCost) - ($projectExpenses + $adminExpenses);

        // Getting unpaid billing
        $cmsBilling = CMSBilling::where('status', 'Unpaid')->sum('amount');

        // Getting a/r
        $cmsAR = CMSAccountReceivable::sum('amount');

        // Getting Income for current month
        $cmsARCurrentMonth = CMSAccountReceivable::whereRaw('MONTH(date) = ?',[$current_month])->sum('amount');
        $downpaymentCurrentMonth = CMSProject::whereRaw('MONTH(date) = ?',[$current_month])->sum('downpayment');
        $incomeCurrentMonth = $cmsARCurrentMonth + $downpaymentCurrentMonth;

        // Sales Transaction Summary
        $trans_today_downpayment = CMSProject::where('date', $current_date)->sum('downpayment');
        $trans_count_today_downpayment = CMSProject::where('date', $current_date)->count();
        $trans_today_ar = CMSAccountReceivable::where('date', $current_date)->sum('amount');
        $trans_count_today_ar = CMSAccountReceivable::where('date', $current_date)->count();
        $today_trans = $trans_today_downpayment + $trans_today_ar;
        $today_trans_count = $trans_count_today_downpayment + $trans_count_today_ar;
        
        $date_yesterday = Carbon::now()->subDays(1)->format('Y-m-d');
        $trans_count_yesterday = CMSProject::where('date', $date_yesterday)->count();
        $trans_yesterday = CMSProject::where('date', $date_yesterday)->sum('downpayment');
        $trans_count_yesterday_ar = CMSAccountReceivable::where('date', $date_yesterday)->count();
        $trans_yesterday_ar = CMSAccountReceivable::where('date', $date_yesterday)->sum('amount');
        $yesterday_trans = $trans_yesterday + $trans_yesterday_ar;
        $yesterday_trans_count = $trans_count_yesterday + $trans_count_yesterday_ar;

        $date_week = Carbon::now()->subDays(7)->format('Y-m-d');
        $trans_count_week = CMSProject::whereDate('date', '>=', $date_week)
        ->whereDate('date', '<=', $current_date)
        ->count();
        $trans_week = CMSProject::whereDate('date', '>=', $date_week)
        ->whereDate('date', '<=', $current_date)
        ->sum('downpayment');
        $trans_count_week_ar = CMSAccountReceivable::whereDate('date', '>=', $date_week)
        ->whereDate('date', '<=', $current_date)
        ->count();
        $trans_week_ar = CMSAccountReceivable::whereDate('date', '>=', $date_week)
        ->whereDate('date', '<=', $current_date)
        ->sum('amount');
        $week_trans = $trans_week + $trans_week_ar;
        $week_trans_count = $trans_count_week + $trans_count_week_ar;

        $trans_count_curr_month = CMSProject::whereRaw('MONTH(date) = ?',[$current_month])
        ->count();
        $trans_curr_month = CMSProject::whereRaw('MONTH(date) = ?',[$current_month])
        ->sum('downpayment');
        $trans_count_curr_month_ar = CMSAccountReceivable::whereRaw('MONTH(date) = ?',[$current_month])
        ->count();
        $trans_curr_month_ar = CMSAccountReceivable::whereRaw('MONTH(date) = ?',[$current_month])
        ->sum('amount');
        $month_trans = $trans_curr_month + $trans_curr_month_ar;
        $month_trans_count = $trans_count_curr_month + $trans_count_curr_month_ar;

        $currentYear = date('Y');
        $trans_count_curr_year = CMSProject::whereRaw('YEAR(date) = ?',[$currentYear])
        ->count();
        $trans_curr_year = CMSProject::whereRaw('YEAR(date) = ?',[$currentYear])
        ->sum('downpayment');
        $trans_count_curr_year_ar = CMSAccountReceivable::whereRaw('YEAR(date) = ?',[$currentYear])
        ->count();
        $trans_curr_year_ar = CMSAccountReceivable::whereRaw('YEAR(date) = ?',[$currentYear])
        ->sum('amount');
        $year_trans = $trans_curr_year + $trans_curr_year_ar;
        $year_trans_count = $trans_count_curr_year + $trans_count_curr_year_ar;
        // End Sales Transaction Summary

        // Top 5 Customers
        $project_count  = "(SELECT COUNT(customer_id) FROM c_m_s_projects project where project.customer_id = cust.id) as project_count";
        $total_cost  = "(SELECT SUM(cost) FROM c_m_s_projects project where project.customer_id = cust.id) as total_cost";

        $top_5_customers = DB::table('c_m_s_projects as project')
        ->join('customers as cust', 'project.customer_id','=','cust.id')
        ->select(DB::raw("project.*, cust.*, cust.id as cust_id, $project_count, $total_cost"))
        ->groupBy('project.customer_id')
        ->orderBy(DB::raw('COUNT(project.customer_id)'), 'DESC')
        ->limit(5)
        ->get();
        // End of Top 5 Customers

        // Recent 5 Income
        $cms_project = CMSProject::from('c_m_s_projects as project')
        ->leftjoin('customers as cust', 'project.customer_id','=','cust.id')
        ->select(DB::raw("project.date, project.name, project.downpayment, 0 as amount, cust.*"));

        $cms_ar = CMSAccountReceivable::from('c_m_s_account_receivables as ar')
        ->leftjoin('customers as cust', 'ar.customer_id','=','cust.id')
        ->leftjoin('c_m_s_projects as project', 'ar.project_id','=','project.id')
        ->select(DB::raw("ar.date, project.name, 0 as downpayment, ar.amount, cust.*"))
        ->unionAll($cms_project)
        ->orderby('date', 'desc')
        ->limit(5)
        ->get();
        // End of Recent 5 Income

        return view('construction-monitoring-system.index', compact(
            'totalNonTaxIncomeToday', 
            'totalNoORExpensesToday',
            'grossIncome',
            'netIncome',
            'cmsBilling',
            'cmsAR',
            'incomeCurrentMonth',
            'today_trans',
            'today_trans_count',
            'yesterday_trans',
            'yesterday_trans_count',
            'week_trans',
            'week_trans_count',
            'month_trans',
            'month_trans_count',
            'year_trans',
            'year_trans_count',
            'top_5_customers',
            'cms_ar',
            'startDate',
            'endDate',
            'projectCost',
            'projAdditionalWorkCost'

        ));
    }

    public function dashboardSearch(Request $request){

        $current_date = date('Y-m-d');
        $current_month = date('m');

        $dateRange = trim($request->date_range);

        $arrayDate = array();

        if($dateRange) { 
            $arrayDate = explode('-', $dateRange);
        }

        $startDate = date('Y-m-d', strtotime($arrayDate[0]));
        $endDate = date('Y-m-d', strtotime($arrayDate[1]));

        // Non tax income for current date
        $total_downpayment = CMSProject::whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        ->sum('downpayment');
        $total_ar = CMSAccountReceivable::whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        ->sum('amount');
        $totalNonTaxIncomeToday = $total_downpayment + $total_ar;

        // No OR expenses for current date
        $totalNoORExpensesToday = CMSExpenses::whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        ->sum('amount');

        // Getting Gross Income
        $projectCost = CMSProject::whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        ->sum('cost');
        $projAdditionalWorkCost = CMSAdditionalWork::whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        ->sum('amount');
        $projectExpenses = CMSExpenses::whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        ->where('expenses_type', 'project expenses')
        ->sum('amount');
        $grossIncome = ($projectCost + $projAdditionalWorkCost) - $projectExpenses;

        // Getting Net Income
        $projectCost = CMSProject::whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        ->sum('cost');
        $projAdditionalWorkCost = CMSAdditionalWork::whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        ->sum('amount');
        $projectExpenses = CMSExpenses::whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        ->where('expenses_type', 'project expenses')
        ->sum('amount');
        $adminExpenses = CMSExpenses::whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        ->where('expenses_type', 'admin expenses')
        ->sum('amount');
        $netIncome = ($projectCost + $projAdditionalWorkCost) - ($projectExpenses + $adminExpenses);

        // Getting unpaid billing
        $cmsBilling = CMSBilling::whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        ->where('status', 'Unpaid')
        ->sum('amount');

        // Getting a/r
        $cmsAR = CMSAccountReceivable::whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        ->sum('amount');

        // Getting Income for current month
        $cmsARCurrentMonth = CMSAccountReceivable::whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        ->sum('amount');
        $downpaymentCurrentMonth = CMSProject::whereDate('date', '>=', $startDate)
        ->whereDate('date', '<=', $endDate)
        ->sum('downpayment');
        $incomeCurrentMonth = $cmsARCurrentMonth + $downpaymentCurrentMonth;

        // Sales Transaction Summary
        $trans_today_downpayment = CMSProject::where('date', $current_date)->sum('downpayment');
        $trans_count_today_downpayment = CMSProject::where('date', $current_date)->count();
        $trans_today_ar = CMSAccountReceivable::where('date', $current_date)->sum('amount');
        $trans_count_today_ar = CMSAccountReceivable::where('date', $current_date)->count();
        $today_trans = $trans_today_downpayment + $trans_today_ar;
        $today_trans_count = $trans_count_today_downpayment + $trans_count_today_ar;
        
        $date_yesterday = Carbon::now()->subDays(1)->format('Y-m-d');
        $trans_count_yesterday = CMSProject::where('date', $date_yesterday)->count();
        $trans_yesterday = CMSProject::where('date', $date_yesterday)->sum('downpayment');
        $trans_count_yesterday_ar = CMSAccountReceivable::where('date', $date_yesterday)->count();
        $trans_yesterday_ar = CMSAccountReceivable::where('date', $date_yesterday)->sum('amount');
        $yesterday_trans = $trans_yesterday + $trans_yesterday_ar;
        $yesterday_trans_count = $trans_count_yesterday + $trans_count_yesterday_ar;

        $date_week = Carbon::now()->subDays(7)->format('Y-m-d');
        $trans_count_week = CMSProject::whereDate('date', '>=', $date_week)
        ->whereDate('date', '<=', $current_date)
        ->count();
        $trans_week = CMSProject::whereDate('date', '>=', $date_week)
        ->whereDate('date', '<=', $current_date)
        ->sum('downpayment');
        $trans_count_week_ar = CMSAccountReceivable::whereDate('date', '>=', $date_week)
        ->whereDate('date', '<=', $current_date)
        ->count();
        $trans_week_ar = CMSAccountReceivable::whereDate('date', '>=', $date_week)
        ->whereDate('date', '<=', $current_date)
        ->sum('amount');
        $week_trans = $trans_week + $trans_week_ar;
        $week_trans_count = $trans_count_week + $trans_count_week_ar;

        $trans_count_curr_month = CMSProject::whereRaw('MONTH(date) = ?',[$current_month])
        ->count();
        $trans_curr_month = CMSProject::whereRaw('MONTH(date) = ?',[$current_month])
        ->sum('downpayment');
        $trans_count_curr_month_ar = CMSAccountReceivable::whereRaw('MONTH(date) = ?',[$current_month])
        ->count();
        $trans_curr_month_ar = CMSAccountReceivable::whereRaw('MONTH(date) = ?',[$current_month])
        ->sum('amount');
        $month_trans = $trans_curr_month + $trans_curr_month_ar;
        $month_trans_count = $trans_count_curr_month + $trans_count_curr_month_ar;

        $currentYear = date('Y');
        $trans_count_curr_year = CMSProject::whereRaw('YEAR(date) = ?',[$currentYear])
        ->count();
        $trans_curr_year = CMSProject::whereRaw('YEAR(date) = ?',[$currentYear])
        ->sum('downpayment');
        $trans_count_curr_year_ar = CMSAccountReceivable::whereRaw('YEAR(date) = ?',[$currentYear])
        ->count();
        $trans_curr_year_ar = CMSAccountReceivable::whereRaw('YEAR(date) = ?',[$currentYear])
        ->sum('amount');
        $year_trans = $trans_curr_year + $trans_curr_year_ar;
        $year_trans_count = $trans_count_curr_year + $trans_count_curr_year_ar;
        // End Sales Transaction Summary

        // Top 5 Customers
        $project_count  = "(SELECT COUNT(customer_id) FROM c_m_s_projects project where project.customer_id = cust.id) as project_count";
        $total_cost  = "(SELECT SUM(cost) FROM c_m_s_projects project where project.customer_id = cust.id) as total_cost";

        $top_5_customers = DB::table('c_m_s_projects as project')
        ->join('customers as cust', 'project.customer_id','=','cust.id')
        ->select(DB::raw("project.*, cust.*, cust.id as cust_id, $project_count, $total_cost"))
        ->groupBy('project.customer_id')
        ->orderBy(DB::raw('COUNT(project.customer_id)'), 'DESC')
        ->limit(5)
        ->get();
        // End of Top 5 Customers

        // Recent 5 Income
        $cms_project = CMSProject::from('c_m_s_projects as project')
        ->leftjoin('customers as cust', 'project.customer_id','=','cust.id')
        ->select(DB::raw("project.date, project.name, project.downpayment, 0 as amount, cust.*"));

        $cms_ar = CMSAccountReceivable::from('c_m_s_account_receivables as ar')
        ->leftjoin('customers as cust', 'ar.customer_id','=','cust.id')
        ->leftjoin('c_m_s_projects as project', 'ar.project_id','=','project.id')
        ->select(DB::raw("ar.date, project.name, 0 as downpayment, ar.amount, cust.*"))
        ->unionAll($cms_project)
        ->orderby('date', 'desc')
        ->limit(5)
        ->get();
        // End of Recent 5 Income

        return view('construction-monitoring-system.index', compact(
            'totalNonTaxIncomeToday', 
            'totalNoORExpensesToday',
            'grossIncome',
            'netIncome',
            'cmsBilling',
            'cmsAR',
            'incomeCurrentMonth',
            'today_trans',
            'today_trans_count',
            'yesterday_trans',
            'yesterday_trans_count',
            'week_trans',
            'week_trans_count',
            'month_trans',
            'month_trans_count',
            'year_trans',
            'year_trans_count',
            'top_5_customers',
            'cms_ar',
            'startDate',
            'endDate',
            'projectCost',
            'projAdditionalWorkCost'
        ));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemController.add')){
            return view('permission-denied');
        }
        // User role permission end here

        return view('construction-monitoring-system.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(empty($request->customer_id)){
            return back()->with('warning','No customer for this project found!!!');
        }
        
        $cost = $request->cost;
        $downpayment = $request->downpayment;

        $cost = str_replace( ',', '', $cost );
        $downpayment = str_replace( ',', '', $downpayment );

        $cmsproject = new CMSProject();
        $cmsproject->customer_id = $request->customer_id;
        $cmsproject->type = $request->type;
        $cmsproject->name = $request->name;
        $cmsproject->cost = $cost;
        $cmsproject->downpayment = $downpayment;
        $cmsproject->date = $request->date;
        $cmsproject->status = $request->status;
        $cmsproject->save();

        return back()->with('success','Project entry is successfully saved.');
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
    public function edit(Request $request, $id)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemController.edit')){
            return view('permission-denied');
        }
        // User role permission end here

        $project = CMSProject::from('c_m_s_projects as cms')
        ->leftjoin('customers as cust', 'cust.id', '=', 'cms.customer_id')
        ->select(DB::raw("cust.*, cms.*, cms.id as id"))
        ->where('cms.id', $id)
        ->first();

        return view('construction-monitoring-system.edit',compact('project'));
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
        
        $cost = $request->cost;
        $downpayment = $request->downpayment;

        $cost = str_replace( ',', '', $cost );
        $downpayment = str_replace( ',', '', $downpayment );

        $cmsproject = CMSProject::find($id);
        $cmsproject->customer_id = $request->customer_id;
        $cmsproject->type = $request->type;
        $cmsproject->name = $request->name;
        $cmsproject->cost = $cost;
        $cmsproject->downpayment = $downpayment;
        $cmsproject->date = $request->date;
        $cmsproject->status = $request->status;
        $cmsproject->save();

        return back()->with('success','Project is successfully updated.');
    }

    public function additionalWork($id){

        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemController.add')){
            return view('permission-denied');
        }
        // User role permission end here

        $project = CMSProject::where('id', $id)->first();
        return view('construction-monitoring-system.additional-work', compact('project'));
    }

    public function saveAdditionalWork(Request $request, $id){

        if(empty($request->customer_id)){
            return back()->with('warning','No customer for this project found!!!');
        }
        
        $amount = $request->amount;

        $amount = str_replace( ',', '', $amount );

        $cms_aw = new CMSAdditionalWork();
        $cms_aw->project_id = $id;
        $cms_aw->customer_id = $request->customer_id;
        $cms_aw->amount = $amount;
        $cms_aw->date = $request->date;
        $cms_aw->description = $request->description;
        $cms_aw->save();

        return back()->with('success','Additional work is successfully saved.');
    }

    public function additionalWorkList(){

        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemController.index')){
            return view('permission-denied');
        }
        // User role permission end here

        $search_additional_works = '';
        $date_from = '';
        $date_to = '';

        $cms_additional_work = CMSAdditionalWork::from('c_m_s_additional_works as aw')
        ->leftjoin('c_m_s_projects as project', 'aw.project_id', '=', 'project.id')
        ->leftjoin('customers as cust', 'aw.customer_id', '=', 'cust.id')
        ->select(DB::raw("aw.*, project.name, project.type, cust.*, aw.date as date, aw.id as id"))
        ->orderby('aw.date', 'desc')
        ->orderby('project.name')
        ->paginate(10);

        $cms_aw_cost = 0;
        foreach($cms_additional_work as $key => $item){
            $cms_aw_cost = $cms_aw_cost + $item->amount;
        }

        $cms_total_aw_amount = CMSAdditionalWork::sum('amount');
        
        return view('construction-monitoring-system.additional-work-list', compact('cms_additional_work','cms_aw_cost','cms_total_aw_amount', 'search_additional_works', 'date_from', 'date_to'));
    }

    public function additionalWorkSearch(Request $request){
        
        $search_additional_works = $request->search_additional_works;
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        if(!empty($search_additional_works) && empty($date_from)){

            $cms_additional_work = CMSAdditionalWork::from('c_m_s_additional_works as aw')
            ->leftjoin('c_m_s_projects as project', 'aw.project_id', '=', 'project.id')
            ->leftjoin('customers as cust', 'aw.customer_id', '=', 'cust.id')
            ->select(DB::raw("aw.*, project.*, cust.*, aw.date as date, aw.id as id"))
            ->where(DB::raw('TRIM(CONCAT(cust.first_name, " ", cust.middle_name, " ", cust.last_name, " ", cust.company_name, " ", project.name, " ", project.type))'), 'LIKE', "%$search_additional_works%")
            ->orderby('aw.date', 'desc')
            ->orderby('project.name')
            ->paginate(10);

            $cms_total_aw_amount = CMSAdditionalWork::from('c_m_s_additional_works as aw')
            ->leftjoin('c_m_s_projects as project', 'aw.project_id', '=', 'project.id')
            ->leftjoin('customers as cust', 'aw.customer_id', '=', 'cust.id')
            ->select(DB::raw("aw.amount, cust.*, project.*"))
            ->where(DB::raw('TRIM(CONCAT(cust.first_name, " ", cust.middle_name, " ", cust.last_name, " ", cust.company_name, " ", project.name, " ", project.type))'), 'LIKE', "%$search_additional_works%")
            ->sum('aw.amount');

        }elseif(!empty($search_additional_works) && !empty($date_from)){

            $cms_additional_work = CMSAdditionalWork::from('c_m_s_additional_works as aw')
            ->leftjoin('c_m_s_projects as project', 'aw.project_id', '=', 'project.id')
            ->leftjoin('customers as cust', 'aw.customer_id', '=', 'cust.id')
            ->select(DB::raw("aw.*, project.*, cust.*, aw.date as date, aw.id as id"))
            ->whereDate('aw.date', '>=', $date_from)
            ->whereDate('aw.date', '<=', $date_to)
            ->where(DB::raw('TRIM(CONCAT(cust.first_name, " ", cust.middle_name, " ", cust.last_name, " ", cust.company_name, " ", project.name, " ", project.type))'), 'LIKE', "%$search_additional_works%")
            ->orderby('aw.date', 'desc')
            ->orderby('project.name')
            ->paginate(10);

            $cms_total_aw_amount = CMSAdditionalWork::from('c_m_s_additional_works as aw')
            ->leftjoin('c_m_s_projects as project', 'aw.project_id', '=', 'project.id')
            ->leftjoin('customers as cust', 'aw.customer_id', '=', 'cust.id')
            ->select(DB::raw("aw.amount, cust.*, project.*"))
            ->whereDate('aw.date', '>=', $date_from)
            ->whereDate('aw.date', '<=', $date_to)
            ->where(DB::raw('TRIM(CONCAT(cust.first_name, " ", cust.middle_name, " ", cust.last_name, " ", cust.company_name, " ", project.name, " ", project.type))'), 'LIKE', "%$search_additional_works%")
            ->sum('aw.amount');

        }else{

            $cms_additional_work = CMSAdditionalWork::from('c_m_s_additional_works as aw')
            ->leftjoin('c_m_s_projects as project', 'aw.project_id', '=', 'project.id')
            ->leftjoin('customers as cust', 'aw.customer_id', '=', 'cust.id')
            ->select(DB::raw("aw.*, project.*, cust.*, aw.date as date, aw.id as id"))
            ->whereDate('aw.date', '>=', $date_from)
            ->whereDate('aw.date', '<=', $date_to)
            ->orderby('aw.date', 'desc')
            ->orderby('project.name')
            ->paginate(10);

            $cms_total_aw_amount = CMSAdditionalWork::from('c_m_s_additional_works as aw')
            ->leftjoin('c_m_s_projects as project', 'aw.project_id', '=', 'project.id')
            ->leftjoin('customers as cust', 'aw.customer_id', '=', 'cust.id')
            ->select(DB::raw("aw.amount, cust.*, project.*"))
            ->whereDate('aw.date', '>=', $date_from)
            ->whereDate('aw.date', '<=', $date_to)
            ->sum('aw.amount');
        }

        $cms_aw_cost = 0;
        foreach($cms_additional_work as $key => $item){
            $cms_aw_cost = $cms_aw_cost + $item->amount;
        }

        return view('construction-monitoring-system.additional-work-list', compact('cms_additional_work','cms_aw_cost','cms_total_aw_amount', 'search_additional_works', 'date_from', 'date_to'));
    }

    public function additionalWorkEdit($id){

        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemController.edit')){
            return view('permission-denied');
        }
        // User role permission end here

        $additional_work = CMSAdditionalWork::from('c_m_s_additional_works as aw')
        ->leftjoin('customers as cust', 'aw.customer_id', '=', 'cust.id')
        ->select(DB::raw("aw.*, cust.*, aw.date as date, aw.id as id"))
        ->where('aw.id', $id)
        ->first();
        
        return view('construction-monitoring-system.additional-work-edit', compact('additional_work'));
    }

    public function additionalWorkUpdate(Request $request, $id){
        
        $amount = $request->amount;

        $amount = str_replace( ',', '', $amount );

        $cms_aw = CMSAdditionalWork::find($id);
        $cms_aw->amount = $amount;
        $cms_aw->date = $request->date;
        $cms_aw->description = $request->description;
        $cms_aw->save();

        return redirect('construction-monitoring-system/additional-work-list')
        ->with('success','Additional work is successfully updated.');
    }

    public function additionalWorkDelete($id){

        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemController.delete')){
            return view('permission-denied');
        }
        // User role permission end here

        CMSAdditionalWork::destroy($id);
        return back()->with('success','Additional work is successfully deleted.');
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
        if(!auth()->user()->canAccess('ConsMonSystemController.delete')){
            return view('permission-denied');
        }
        // User role permission end here

        CMSExpenses::where('project_id', $id)->delete();
        CMSAccountReceivable::where('project_id', $id)->delete();
        CMSBilling::where('project_id', $id)->delete();
        CMSAdditionalWork::where('project_id', $id)->delete();
        CMSProject::destroy($id);
        
        return back()->with('success','Project is successfully deleted.');
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

    public function projectList(Request $request){

        // User role permission start here
        if(!auth()->user()->canAccess('ConsMonSystemController.index')){
            return view('permission-denied');
        }
        // User role permission end here

        $search_projects_customers_types = $request->search_projects_customers_types;
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        $project_list = CMSProject::from('c_m_s_projects as cms')
        ->leftjoin('customers as cust', 'cust.id', '=', 'cms.customer_id')
        ->select(DB::raw("cust.*, cms.*, cms.id as id"))
        ->orderby('cms.date', 'desc')
        ->orderby('cms.name', 'asc')
        ->paginate(10);

        $total_cost = 0;
        $total_downpayment = 0;
        $total_balance = 0;

        foreach($project_list as $key => $item){

            $total_cost = $total_cost + $item->cost;
            $total_downpayment = $total_downpayment + $item->downpayment;
            $total_balance = $total_balance + ($item->cost - $item->downpayment);
        }

        $total_project_cost = CMSProject::from('c_m_s_projects as cms')
        ->leftjoin('customers as cust', 'cust.id', '=', 'cms.customer_id')
        ->select(DB::raw("cust.*, cms.*, cms.id as id"))
        ->sum('cms.cost');

        $total_project_downpayment = CMSProject::from('c_m_s_projects as cms')
        ->leftjoin('customers as cust', 'cust.id', '=', 'cms.customer_id')
        ->select(DB::raw("cust.*, cms.*, cms.id as id"))
        ->sum('cms.downpayment');

        $total_project_balance = 0;
        $total_project_balance  = $total_project_cost - $total_project_downpayment;

        return view('construction-monitoring-system.project-list', compact(
            'project_list', 
            'search_projects_customers_types', 
            'date_from', 
            'date_to',
            'total_cost',
            'total_downpayment',
            'total_balance',
            'total_project_cost',
            'total_project_downpayment',
            'total_project_balance'
        ));
    }

    public function projectSearch(Request $request){

        $search_projects_customers_types = $request->search_projects_customers_types;
        $date_from = $request->date_from;
        $date_to = $request->date_to;

        if(!empty($search_projects_customers_types) && empty($date_from)){

            $project_list = CMSProject::from('c_m_s_projects as cms')
            ->leftjoin('customers as cust', 'cust.id', '=', 'cms.customer_id')
            ->select(DB::raw("cust.*, cms.*, cms.id as id"))
            ->where(DB::raw('TRIM(CONCAT(cust.first_name, " ", cust.middle_name, " ", cust.last_name, " ", cust.company_name, " ", cms.name, " ", cms.type))'), 'LIKE', "%$search_projects_customers_types%")
            ->orderby('cms.date', 'desc')
            ->orderby('cms.name', 'asc')
            ->paginate(10);

            $total_project_cost = CMSProject::from('c_m_s_projects as cms')
            ->leftjoin('customers as cust', 'cust.id', '=', 'cms.customer_id')
            ->select(DB::raw("cust.*, cms.*, cms.id as id"))
            ->where(DB::raw('TRIM(CONCAT(cust.first_name, " ", cust.middle_name, " ", cust.last_name, " ", cust.company_name, " ", cms.name, " ", cms.type))'), 'LIKE', "%$search_projects_customers_types%")
            ->sum('cms.cost');

            $total_project_downpayment = CMSProject::from('c_m_s_projects as cms')
            ->leftjoin('customers as cust', 'cust.id', '=', 'cms.customer_id')
            ->select(DB::raw("cust.*, cms.*, cms.id as id"))
            ->where(DB::raw('TRIM(CONCAT(cust.first_name, " ", cust.middle_name, " ", cust.last_name, " ", cust.company_name, " ", cms.name, " ", cms.type))'), 'LIKE', "%$search_projects_customers_types%")
            ->sum('cms.downpayment');

        }elseif(!empty($search_projects_customers_types) && !empty($date_from)){

            $project_list = CMSProject::from('c_m_s_projects as cms')
            ->leftjoin('customers as cust', 'cust.id', '=', 'cms.customer_id')
            ->select(DB::raw("cust.*, cms.*, cms.id as id"))
            ->whereDate('cms.date', '>=', $date_from)
            ->whereDate('cms.date', '<=', $date_to)
            ->where(DB::raw('TRIM(CONCAT(cust.first_name, " ", cust.middle_name, " ", cust.last_name, " ", cust.company_name, " ", cms.name, " ", cms.type))'), 'LIKE', "%$search_projects_customers_types%")
            ->orderby('cms.date', 'desc')
            ->orderby('cms.name', 'asc')
            ->paginate(10);

            $total_project_cost = CMSProject::from('c_m_s_projects as cms')
            ->leftjoin('customers as cust', 'cust.id', '=', 'cms.customer_id')
            ->select(DB::raw("cust.*, cms.*, cms.id as id"))
            ->whereDate('cms.date', '>=', $date_from)
            ->whereDate('cms.date', '<=', $date_to)
            ->where(DB::raw('TRIM(CONCAT(cust.first_name, " ", cust.middle_name, " ", cust.last_name, " ", cust.company_name, " ", cms.name, " ", cms.type))'), 'LIKE', "%$search_projects_customers_types%")
            ->sum('cms.cost');

            $total_project_downpayment = CMSProject::from('c_m_s_projects as cms')
            ->leftjoin('customers as cust', 'cust.id', '=', 'cms.customer_id')
            ->select(DB::raw("cust.*, cms.*, cms.id as id"))
            ->whereDate('cms.date', '>=', $date_from)
            ->whereDate('cms.date', '<=', $date_to)
            ->where(DB::raw('TRIM(CONCAT(cust.first_name, " ", cust.middle_name, " ", cust.last_name, " ", cust.company_name, " ", cms.name, " ", cms.type))'), 'LIKE', "%$search_projects_customers_types%")
            ->sum('cms.downpayment');

        }else{

            $project_list = CMSProject::from('c_m_s_projects as cms')
            ->leftjoin('customers as cust', 'cust.id', '=', 'cms.customer_id')
            ->select(DB::raw("cust.*, cms.*, cms.id as id"))
            ->whereDate('cms.date', '>=', $date_from)
            ->whereDate('cms.date', '<=', $date_to)
            ->orderby('cms.date', 'desc')
            ->orderby('cms.name', 'asc')
            ->paginate(10);

            $total_project_cost = CMSProject::from('c_m_s_projects as cms')
            ->leftjoin('customers as cust', 'cust.id', '=', 'cms.customer_id')
            ->select(DB::raw("cust.*, cms.*, cms.id as id"))
            ->whereDate('cms.date', '>=', $date_from)
            ->whereDate('cms.date', '<=', $date_to)
            ->sum('cms.cost');

            $total_project_downpayment = CMSProject::from('c_m_s_projects as cms')
            ->leftjoin('customers as cust', 'cust.id', '=', 'cms.customer_id')
            ->select(DB::raw("cust.*, cms.*, cms.id as id"))
            ->whereDate('cms.date', '>=', $date_from)
            ->whereDate('cms.date', '<=', $date_to)
            ->sum('cms.downpayment');
        }

        $total_cost = 0;
        $total_downpayment = 0;
        $total_balance = 0;

        foreach($project_list as $key => $item){

            $total_cost = $total_cost + $item->cost;
            $total_downpayment = $total_downpayment + $item->downpayment;
            $total_balance = $total_balance + ($item->cost - $item->downpayment);
        }

        $total_project_balance = 0;
        $total_project_balance  = $total_project_cost - $total_project_downpayment;

        return view('construction-monitoring-system.project-list', compact(
            'project_list', 
            'search_projects_customers_types', 
            'date_from', 
            'date_to',
            'total_cost',
            'total_downpayment',
            'total_balance',
            'total_project_cost',
            'total_project_downpayment',
            'total_project_balance'
        ));
    }
}
