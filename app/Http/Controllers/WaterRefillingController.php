<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
//use App\Http\Requests;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\WaterRefilling;
use App\WaterBottleRegenerates;
use App\WRMRegenerateSettings;
use App\WRMOriginalBottles;
use App\WRMDamageBottles;
use App\WRMChangesapfiltercounter;
use App\WRMChangesapfilter;
use App\Customer;
use App\Product;
use App\WRMExpenses;
use App\WRMChangesapfiltercounteralkaline;
use App\WRMChangesapfiltercountermineral;
use Carbon\Carbon;
use DateTime;
use DB;
use Cashadvance;
use Artisan;
//use Session;

class WaterRefillingController extends Controller
{

    public function __construct()

    {

        $this->middleware('auth');

        

        //$this->mdOption                = new Option;

        //$this->mdOption->prefix        = 'cashinvoice_';

        //$this->mdOption->accountFields = ['coa_debit', 'tax_debit', 'discount_debit', 'coa_credit', 'coa_credit2', 'coa_credit3', 'coa_credit4', 'tax_credit'];

        

        // dd( $this->mdOption->getCoas('coa_debit') );

        // dd( ChartAccount::getByIds( [1,2] ) );

    }



    /**

     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request){

        // Artisan::call('config:clear');
        // Artisan::call('config:cache');

        $date = Carbon::parse($request->ShootDateTime)->timezone('Asia/Manila');
        $date = $date->format('Y-m-d');

        $bots_regenerates = DB::table('water_bottle_regenerates')
        ->orderBy('created_at', 'ASC')->get();

        $amount_due = DB::table('water_refillings')->select(DB::raw('*'))
        ->where('date', $date)->get();
        
        $bottles_issued = DB::table('water_refillings')->get();
        
        $expenses_today = DB::table('w_r_m_expenses')->select(DB::raw('*'))
        ->where('date', $date)->get();
        
        $currentMonth = date('m');
        $over_all_sales = DB::table('water_refillings')
        ->whereRaw('MONTH(date) = ?',[$currentMonth])
        ->sum('amount_due');
        // dd($over_all_sales);

        $over_all_no_OR_expenses = DB::table('w_r_m_expenses')
        ->whereRaw('MONTH(date) = ?',[$currentMonth])
        ->sum('amount');
        // dd($over_all_no_OR_expenses);

        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now();
        $ca_expenses = DB::table('chart_accounts as ca')
        ->join('vouchers as v', 'ca.id','=','v.chart_account_id')
        ->select(DB::raw('IFNULL(SUM(debit-credit), 0) as ca_expenses'))
        ->whereBetween('v.date',[$start, $end])
        ->where('ca.account_type_id', 9)
        ->get();
        // dd($ca_expenses);

        $regenerate_settings = DB::table('w_r_m_regenerate_settings')->orderBy('created_at', 'desc')->first();
        $changesapfilter_settings = DB::table('w_r_m_changesapfilters')->orderBy('created_at', 'desc')->first();
        $changesapfilteralkaline_settings = DB::table('w_r_m_changesapfiltercounteralkalines')->orderBy('created_at', 'desc')->first();
        $changesapfiltermineral_settings = DB::table('w_r_m_changesapfiltercounterminerals')->orderBy('created_at', 'desc')->first();

        $sum_alkaline_bottles = DB::table('w_r_m_changesapfiltercounters')->select(DB::raw('sum(order_qty + refill_bottle + container_qty + dealer_qty) as alkaline'))
        ->where('type_filter', 'LIKE', "%alkaline%")->first();
        $sum_purified_bottles = DB::table('w_r_m_changesapfiltercounters')->select(DB::raw('sum(order_qty + refill_bottle + container_qty + dealer_qty) as purified'))
        ->where('type_filter', 'LIKE', "%purified%")
        ->orWhere('type_filter', 'LIKE', "")->first();
        $sum_mineral_bottles = DB::table('w_r_m_changesapfiltercounters')->select(DB::raw('sum(order_qty + refill_bottle + container_qty + dealer_qty) as mineral'))
        ->where('type_filter', 'LIKE', "%mineral%")->first();
        
        $sum_original_bottles = DB::table('w_r_m_original_bottles')->sum('orig_bottles');
        $sum_damage_bottles = DB::table('w_r_m_damage_bottles')->sum('dmg_bottles');
        $sum_order_bottles = DB::table('water_refillings')->sum('order_qty');
        $sum_return_bottles = DB::table('water_refillings')->sum('return_bottle');
        $sum_container_sold = DB::table('water_refillings')->sum('container_qty');

        $customer_unpaid = DB::table('water_refillings')
        ->whereRaw('MONTH(date) = ?',[$currentMonth])
        ->where('status', 'Unpaid')
        ->Orwhere('status', 'Balanced')
        ->sum('amt_balance');

        $cash_advance = DB::table('cashadvances')
        ->whereRaw('MONTH(date) = ?',[$currentMonth])
        ->where('keys', 'ca')
        ->sum('ca_amount');
        // dd($cash_advance);

        $num_unpaid_bottles = DB::table('water_refillings')->select(DB::raw('sum(order_qty + refill_bottle + dealer_qty) as num_unpaid_bottles'))
        ->whereRaw('MONTH(date) = ?',[$currentMonth])
        ->where('status', 'Unpaid')
        ->first();

        return view('water-refilling-monitoring.index', compact(
            'bots_regenerates',
            'amount_due',
            'bottles_issued',
            'expenses_today',
            'regenerate_settings',
            'sum_original_bottles',
            'sum_damage_bottles',
            'sum_order_bottles',
            'sum_return_bottles',
            'sum_container_sold',
            'changesapfilter_settings',
            'changesapfilteralkaline_settings',
            'changesapfiltermineral_settings',
            'sum_alkaline_bottles',
            'sum_purified_bottles',
            'sum_mineral_bottles',
            'over_all_no_OR_expenses',
            'over_all_sales',
            'ca_expenses',
            'customer_unpaid',
            'num_unpaid_bottles',
            'cash_advance'
        ));
    }

    public function create(Request $request){
        // This will generate the latest entry_no from database
    	$water_refillings = DB::table('water_refillings')->orderBy('entry_no', 'DESC')->first();
        return view('water-refilling-monitoring.create',compact('water_refillings'));
    }

    
    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request){
        
        $this->validate($request, [
            'entry_no' => 'required',
            'pro_id' => '',
            'customer_id' => 'required',
            'return_bottle' => 'required',
            'order_qty' => 'required',
            'refill_bottle' => 'required',
            'others_qty' => 'required',
            'container_qty' => 'required',
            'dealer_qty' => 'required',
            'amount_due' => 'required',
            'date' => 'required',
            'status' => '',
            'amt_balance' => 'required',
        ]);
        
        WaterRefilling::create($request->all());
     
        // $product = $request->all();
        // dd($product);
        // //if($ci && count($request->products)) {

        //     foreach($product[''] as $product) {

        //         $product['ref_id']       = $ci->id; 

        //         WaterRefilling::create($product);
        //     }

        // //}

        // dd("Done");

    
        $this->validate($request, [
            'order_qty' => 'required',
            'refill_bottle' => 'required',
            'container_qty' => 'required',
            'dealer_qty' => 'required',
        ]);
        WaterBottleRegenerates::create($request->all());

        $this->validate($request, [
            'order_qty' => 'required',
            'refill_bottle' => 'required',
            'container_qty' => 'required',
            'dealer_qty' => 'required',
            'type_filter' => ''
        ]);
        WRMChangesapfiltercounter::create($request->all());

        return redirect()->route('water-refilling-monitoring.create')
                        ->with('success','New entry added');
    }
        //Session::flash('flash_message', 'New entry added');
        //return redirect('water-refilling-monitoring.create');

    public function edit($id){

        $customer_id = 0;

        $wrm_records = WaterRefilling::findOrFail($id);

        $customer_id = $wrm_records->customer_id;

        $customer_info = Customer::select('*')
        ->where('id', '=', $customer_id)
        ->first();

        return view('water-refilling-monitoring.edit', compact('wrm_records','customer_info'));
    }

    public function update($id, Request $request){
        $wrm_records = WaterRefilling::findOrFail($id);
        $wrm_records->update($request->all());

        return redirect('water-refilling-monitoring/reports')
            ->with('success','Record is successfully updated.');
    }

	public function show($id){
        $customer_id = 0;

        $wrm_records = WaterRefilling::findOrFail($id);

        $customer_id = $wrm_records->customer_id;
        //$pro_id = $wrm_records->pro_id;

        $customer_info = Customer::select('*')
        ->where('id', '=', $customer_id)
        ->first();

        return view('water-refilling-monitoring.show', compact('wrm_records','customer_info'));
	}

    public function reportsByDetails(Request $request)
    {   
        //return $this->reportsByName($request);

        /*$customer_reports = DB::table('water_refillings')
        ->leftjoin('customers', 'customers.id', '=', 'water_refillings.customer_id')
        ->leftjoin('products', 'products.id', '=', 'water_refillings.pro_id')
        ->select('customers.*', 'water_refillings.*', 'products.*', 'water_refillings.id as rec_id')
        ->orderBy('customers.last_name', 'ASC')
        ->paginate(10);
        //dd( $customer_reports );

        return view('water-refilling-monitoring.reports', compact('customer_reports'))
        ->with('i', ($request->input('page', 1) - 1) * 10);*/
    }
   
    public function reports(Request $request)
    {
        
        $search_cust_name = $request->search_cust_name;
        $start = $request->date_from;
        $end = $request->date_to;
        $viewstatus = $request->viewstatus;

        if($viewstatus){
            $customer_reports = DB::table('water_refillings')
            ->leftjoin('customers', 'customers.id', '=', 'water_refillings.customer_id')
            ->leftjoin('products', 'products.id', '=', 'water_refillings.pro_id')
            ->select('customers.*', 'water_refillings.*', 'products.*', 'water_refillings.id as rec_id')
            ->where('water_refillings.status', '=', $viewstatus)
            ->orderBy('customers.last_name', 'ASC')
            ->paginate(10);
    
            $customer_reports_no_pagination = DB::table('water_refillings')
            ->leftjoin('customers', 'customers.id', '=', 'water_refillings.customer_id')
            ->leftjoin('products', 'products.id', '=', 'water_refillings.pro_id')
            ->select('customers.*', 'water_refillings.*', 'products.*', 'water_refillings.id as rec_id')
            ->where('water_refillings.status', '=', $viewstatus)
            ->orderBy('customers.last_name', 'ASC')
            ->get();

            return view('water-refilling-monitoring.reports', compact(
                'customer_reports',
                'start',
                'end',
                'search_cust_name',
                'viewstatus',
                'customer_reports_no_pagination'
                )
            )->with('i', ($request->input('page', 1) - 1) * 5);
        }
        elseif(!empty($search_cust_name)){
            $customer_reports = DB::table('water_refillings')
            ->leftjoin('customers', 'customers.id', '=', 'water_refillings.customer_id')
            ->leftjoin('products', 'products.id', '=', 'water_refillings.pro_id')
            ->select('customers.*', 'water_refillings.*', 'products.*', 'water_refillings.id as rec_id')
            ->where(DB::raw('TRIM(CONCAT(customers.first_name, " ", customers.middle_name, " ", customers.last_name, " ", customers.company_name))'), 'LIKE', "%$search_cust_name%")
            ->orderBy('water_refillings.created_at', 'ASC')
            ->paginate(10);

            $customer_reports_no_pagination = DB::table('water_refillings')
            ->leftjoin('customers', 'customers.id', '=', 'water_refillings.customer_id')
            ->leftjoin('products', 'products.id', '=', 'water_refillings.pro_id')
            ->select('customers.*', 'water_refillings.*', 'products.*', 'water_refillings.id as rec_id')
            ->where(DB::raw('TRIM(CONCAT(customers.first_name, " ", customers.middle_name, " ", customers.last_name, " ", customers.company_name))'), 'LIKE', "%$search_cust_name%")
            ->orderBy('water_refillings.created_at', 'ASC')
            ->get();

           return view('water-refilling-monitoring.reports', compact(
                'customer_reports',
                'start',
                'end',
                'search_cust_name',
                'viewstatus',
                'customer_reports_no_pagination'
                )
            )->with('i', ($request->input('page', 1) - 1) * 5);
        }
        elseif(!empty($start) && !empty($end)){
            $customer_reports = DB::table('water_refillings')
            ->leftjoin('customers', 'customers.id', '=', 'water_refillings.customer_id')
            ->leftjoin('products', 'products.id', '=', 'water_refillings.pro_id')
            ->select('customers.*', 'water_refillings.*', 'products.*', 'water_refillings.id as rec_id')
            ->whereBetween('water_refillings.date', array($start, $end))
            ->orderBy('customers.last_name', 'ASC')
            ->orderBy('customers.company_name', 'ASC')
            ->paginate(10);

            $customer_reports_no_pagination = DB::table('water_refillings')
            ->leftjoin('customers', 'customers.id', '=', 'water_refillings.customer_id')
            ->leftjoin('products', 'products.id', '=', 'water_refillings.pro_id')
            ->select('customers.*', 'water_refillings.*', 'products.*', 'water_refillings.id as rec_id')
            ->whereBetween('water_refillings.date', array($start, $end))
            ->orderBy('customers.last_name', 'ASC')
            ->orderBy('customers.company_name', 'ASC')
            ->get();

           return view('water-refilling-monitoring.reports', compact(
                'customer_reports',
                'start',
                'end',
                'search_cust_name',
                'viewstatus',
                'customer_reports_no_pagination'
                )
            )->with('i', ($request->input('page', 1) - 1) * 5);
        }
        else{
            $customer_reports = DB::table('water_refillings')
            ->leftjoin('customers', 'customers.id', '=', 'water_refillings.customer_id')
            ->leftjoin('products', 'products.id', '=', 'water_refillings.pro_id')
            ->select('customers.*', 'water_refillings.*', 'products.*', 'water_refillings.id as rec_id')
            ->orderBy('customers.last_name', 'ASC')
            ->orderBy('customers.company_name', 'ASC')
            ->paginate(10);
    
            $customer_reports_no_pagination = DB::table('water_refillings')
            ->leftjoin('customers', 'customers.id', '=', 'water_refillings.customer_id')
            ->leftjoin('products', 'products.id', '=', 'water_refillings.pro_id')
            ->select('customers.*', 'water_refillings.*', 'products.*', 'water_refillings.id as rec_id')
            ->orderBy('customers.last_name', 'ASC')
            ->orderBy('customers.company_name', 'ASC')
            ->get();

            return view('water-refilling-monitoring.reports', compact(
                'customer_reports',
                'start',
                'end',
                'search_cust_name',
                'viewstatus',
                'customer_reports_no_pagination'
                )
            )->with('i', ($request->input('page', 1) - 1) * 5);
        }
    }
        

    function findCustomerOnly(Request $request)
    {

        $s = $request->cust_name;

        if(!$s) return [];

        $customer = Customer::select('*', DB::raw('(SELECT sum(return_bottle) FROM water_refillings WHERE customer_id = customers.id) as return_bottle'),
            DB::raw('(SELECT sum(order_qty) FROM water_refillings WHERE customer_id = customers.id) as order_qty'),
            DB::raw('CONCAT(`first_name`," ",`middle_name`," ",`last_name`) as full_name'))
            ->where('individual', '=', 1)
            ->where(DB::raw('TRIM(CONCAT(first_name, " ", middle_name, " ", last_name))'), 'LIKE', "%$s%")
            ->first();

        if(!$customer) {
        $customer = Customer::select('*', DB::raw('(SELECT sum(return_bottle) FROM water_refillings WHERE customer_id = customers.id) as return_bottle'),
            DB::raw('(SELECT sum(order_qty) FROM water_refillings WHERE customer_id = customers.id) as order_qty'),
            DB::raw('company_name as full_name'))
            ->where('individual', '=', 0)
            ->where('company_name', 'LIKE', "%$s%")
            ->first();
        }
        return $customer? $customer: [];
    }

    function findProductAutoSuggest(Request $request){

        $s = $request->term;
        $product = Product::select('id', 'name', 'sr_priority')
            ->where('name', 'LIKE', "%$s%")
            ->orderBy('name')
            ->orderBy('sr_priority','ASC');
        $products = $product->get();
        $response = [];
        foreach($products as $c){
            if(empty($c->sr_priority)){
                $response[] = [ 'id' => $c->id, 'label' => $c->name, 'value' => $c->name ];
            }else{
                $response[] = [ 'id' => $c->id, 'label' => $c->name.' -> '.$c->sr_priority, 'value' => $c->name ];
            }
        }
        return $response;
    }

    function findProduct(Request $request){
        $s = $request->s;


        $total_added_qty = "(SELECT SUM(added_qty) FROM inventories inv where prod.id = inv.pro_id) as total_added_qty";
        $sold_qty  = "(SELECT SUM(order_qty + container_qty + dealer_qty + others_qty) FROM water_refillings wr where prod.id = wr.pro_id) as sold_qty";
    
        if(!empty($request->prod_id)) {

            $searchproduct  = Product::from('products as prod')
            ->select(DB::raw("prod.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
            ->where('prod.id', $request->prod_id)
            ->first();

            // $searchproduct = Product::find($request->prod_id);
            if($searchproduct) 
                $searchproduct->name = trim( "{$searchproduct->name}");

            return $searchproduct? $searchproduct: [];
        }

        if(!$s) return [];

        // $searchproduct = Product::where('name', 'LIKE', "$s%")
        $searchproduct  = Product::from('products as prod')
        ->select(DB::raw("prod.*, prod.id as pro_id, $sold_qty, $total_added_qty"))
        ->where('prod.name', 'LIKE', "$s%")
        ->first();
        
        return $searchproduct? $searchproduct: [];
    }

    public function getRecordDestroy($id)    {
        WaterRefilling::find($id)->delete();
            return redirect('water-refilling-monitoring/reports')
                ->with('success','Record is successfully deleted');
    }

    public function truncate(){
        $empty_bots = WaterBottleRegenerates::query()->truncate();
        return redirect('water-refilling-monitoring')
        ->with('success', 'Resetting regenerate bottles is done.');
    }

    public function reset(){
        //$empty_bots = WRMChangesapfiltercounter::query()->truncate();
        $empty_bots = WRMChangesapfiltercounter::where('type_filter','LIKE',"%purified%")
        ->orWhere('type_filter','LIKE',"")
        ->delete();
        return redirect('water-refilling-monitoring')
        ->with('success', 'Changing SAP Purified filter is done.');
    }

    public function resetalkaline(){
        //$empty_bots = WRMChangesapfiltercounter::query()->truncate();
        $empty_bots = WRMChangesapfiltercounter::where('type_filter','LIKE',"%alkaline%")->delete();
        return redirect('water-refilling-monitoring')
        ->with('success', 'Changing SAP Alkaline filter is done.');
    }

    public function resetmineral(){
        //$empty_bots = WRMChangesapfiltercounter::query()->truncate();
        $empty_bots = WRMChangesapfiltercounter::where('type_filter','LIKE',"%mineral%")->delete();
        return redirect('water-refilling-monitoring')
        ->with('success', 'Changing SAP Mineral filter is done.');
    }

    public function paid($id){
        $paid = WaterRefilling::find($id)->update([
            'amt_balance' => null,
            'status' => 'Paid']
        );
        return redirect('water-refilling-monitoring/reports')
        ->with('success', 'Paying balance is done.'); 
    }

    public function searchDateRange(Request $request){
        
        $date = Carbon::parse($request->ShootDateTime)->timezone('Asia/Manila');
        $date = $date->format('Y-m-d');

        $dateRange = trim($request->date_range);

        $arrayDate = array();

        if($dateRange) { 
            $arrayDate = explode('-', $dateRange);
        }

        $startDate = date('Y-m-d', strtotime($arrayDate[0]));
        $endDate = date('Y-m-d', strtotime($arrayDate[1]));

        $expenses_today = DB::table('w_r_m_expenses')->select(DB::raw('*'))
        ->whereBetween('date', array($startDate, $endDate))
        ->get();

        $bots_regenerates = DB::table('water_bottle_regenerates')
        ->orderBy('created_at', 'ASC')->get();

        $amount_due = DB::table('water_refillings')->select(DB::raw('*'))
        ->whereBetween('date', array($startDate, $endDate))
        ->get();

        // $bottles_issued = DB::table('water_refillings')->get();
        $bottles_issued = DB::table('water_refillings')
        ->whereBetween('date', array($startDate, $endDate))
        ->get();

        $regenerate_settings = DB::table('w_r_m_regenerate_settings')->orderBy('created_at', 'desc')->first();
        $changesapfilter_settings = DB::table('w_r_m_changesapfilters')->orderBy('created_at', 'desc')->first();
        $changesapfilteralkaline_settings = DB::table('w_r_m_changesapfiltercounteralkalines')->orderBy('created_at', 'desc')->first();
        $changesapfiltermineral_settings = DB::table('w_r_m_changesapfiltercounterminerals')->orderBy('created_at', 'desc')->first();

        $sum_alkaline_bottles = DB::table('w_r_m_changesapfiltercounters')->select(DB::raw('sum(order_qty + refill_bottle + container_qty + dealer_qty) as alkaline'))
        ->where('type_filter', 'LIKE', "%alkaline%")->first();
        $sum_purified_bottles = DB::table('w_r_m_changesapfiltercounters')->select(DB::raw('sum(order_qty + refill_bottle + container_qty + dealer_qty) as purified'))
        ->where('type_filter', 'LIKE', "%purified%")
        ->orWhere('type_filter', 'LIKE', "")->first();
        $sum_mineral_bottles = DB::table('w_r_m_changesapfiltercounters')->select(DB::raw('sum(order_qty + refill_bottle + container_qty + dealer_qty) as mineral'))
        ->where('type_filter', 'LIKE', "%mineral%")->first();

        // $sum_original_bottles = DB::table('w_r_m_original_bottles')->sum('orig_bottles');
        $sum_original_bottles = DB::table('w_r_m_original_bottles')
        ->whereBetween('date', array($startDate, $endDate))
        ->sum('orig_bottles');
        // ->get();

        // $sum_damage_bottles = DB::table('w_r_m_damage_bottles')->sum('dmg_bottles');
        $sum_damage_bottles = DB::table('w_r_m_damage_bottles')
        ->whereBetween('date', array($startDate, $endDate))
        ->sum('dmg_bottles');

        // $sum_order_bottles = DB::table('water_refillings')->sum('order_qty');
        $sum_order_bottles = DB::table('water_refillings')
        ->whereBetween('date', array($startDate, $endDate))
        ->sum('order_qty');

        // $sum_return_bottles = DB::table('water_refillings')->sum('return_bottle');
        $sum_return_bottles = DB::table('water_refillings')
        ->whereBetween('date', array($startDate, $endDate))
        ->sum('return_bottle');

        // $sum_container_sold = DB::table('water_refillings')->sum('container_qty');
        $sum_container_sold = DB::table('water_refillings')
        ->whereBetween('date', array($startDate, $endDate))
        ->sum('container_qty');

        $over_all_sales = DB::table('water_refillings')
        ->whereBetween('date', array($startDate, $endDate))
        ->sum('amount_due');

        $over_all_no_OR_expenses = DB::table('w_r_m_expenses')
        ->whereBetween('date', array($startDate, $endDate))
        ->sum('amount');

        $ca_expenses = DB::table('chart_accounts as ca')
        ->join('vouchers as v', 'ca.id','=','v.chart_account_id')
        ->select(DB::raw('IFNULL(SUM(debit-credit), 0) as ca_expenses'))
        ->whereBetween('v.date',[$startDate, $endDate])
        ->where('ca.account_type_id', 9)
        ->get();

        $customer_unpaid = DB::table('water_refillings')
        ->whereBetween('date', array($startDate, $endDate))
        ->where('status', 'Unpaid')
        ->sum('amt_balance');

        $cash_advance = DB::table('cashadvances')
        ->whereBetween('date', array($startDate, $endDate))
        ->where('keys', 'ca')
        ->sum('ca_amount');
        // dd($cash_advance);

        $num_unpaid_bottles = DB::table('water_refillings')->select(DB::raw('sum(order_qty + refill_bottle + dealer_qty) as num_unpaid_bottles'))
        ->whereBetween('date', array($startDate, $endDate))
        ->where('status', 'Unpaid')
        ->first();

        return view('water-refilling-monitoring.index', compact(
            'bots_regenerates',
            'amount_due',
            'bottles_issued',
            'expenses_today',
            'regenerate_settings',
            'sum_original_bottles',
            'sum_damage_bottles',
            'sum_order_bottles',
            'sum_return_bottles',
            'sum_container_sold',
            'changesapfilter_settings',
            'changesapfilteralkaline_settings',
            'changesapfiltermineral_settings',
            'sum_alkaline_bottles',
            'sum_purified_bottles',
            'sum_mineral_bottles',
            'startDate',
            'endDate',
            'over_all_sales',
            'over_all_no_OR_expenses',
            'ca_expenses',
            'customer_unpaid',
            'num_unpaid_bottles',
            'cash_advance'
        ));
        
        return view('water-refilling-monitoring.index', compact('expenses_today'));
    }

    public function wrmCharts(){
        return view('water-refilling-monitoring.wrm-charts');
    }
}