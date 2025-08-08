<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon;
use DB;

class CMSProject extends Model
{
    public $fillable = ['customer_id', 'type', 'name', 'cost', 'downpayment', 'date', 'status'];

    public static function CMSChart() {
		
        $firstDayofYear = new Carbon\Carbon('first day of January');
        $thisDay = Carbon\Carbon::now();
		
        $month = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $charts = $cms_aw_cost_arr = $cms_ar_arr = $cms_dp_arr = $cost_arr = $ca_expense_arr = $cms_expense_arr = $tax_arr = array();
        $cms_aw=$cms_a=$cms_d=$c=$ce=$t=$cms_e = 0;
        $dateFrom = date('n', strtotime($firstDayofYear));
		$dateTo = date('n', strtotime($thisDay));

        $cms_dp = DB::table('c_m_s_projects')
        ->select(DB::raw('DATE_FORMAT(date, "%b") as month'), 
            DB::raw('IFNULL(SUM(downpayment), 0) as downpayment'))
        ->whereBetween('date',[$firstDayofYear, $thisDay])
        ->orderBy('date', 'asc')
        ->groupBy(DB::raw('DATE_FORMAT(date, "%b")'))
        ->get();

        $cms_ar = DB::table('c_m_s_account_receivables')
        ->select(DB::raw('DATE_FORMAT(date, "%b") as month'), 
            DB::raw('IFNULL(SUM(amount), 0) as ar'))
        ->whereBetween('date',[$firstDayofYear, $thisDay])
        ->orderBy('date', 'asc')
        ->groupBy(DB::raw('DATE_FORMAT(date, "%b")'))
        ->get();
        
        $costs = DB::table('c_m_s_projects')
        ->select(DB::raw('DATE_FORMAT(date, "%b") as month'), 
                DB::raw('IFNULL(SUM(cost), 0) as costs'))
        ->whereBetween('date',[$firstDayofYear, $thisDay])
        ->orderBy('date', 'asc')
        ->groupBy(DB::raw('DATE_FORMAT(date, "%b")'))
        ->get();

        $aw_costs = DB::table('c_m_s_additional_works')
        ->select(DB::raw('DATE_FORMAT(date, "%b") as month'), 
                DB::raw('IFNULL(SUM(amount), 0) as aw_costs'))
        ->whereBetween('date',[$firstDayofYear, $thisDay])
        ->orderBy('date', 'asc')
        ->groupBy(DB::raw('DATE_FORMAT(date, "%b")'))
        ->get();
        // dd($aw_costs);
        // $costs = DB::table('chart_accounts as ca')
        //         ->join('vouchers as v', 'ca.id','=','v.chart_account_id')
        //         ->select(DB::raw('DATE_FORMAT(v.date, "%b") as month'),
        //                 DB::raw('IFNULL(SUM(debit-credit), 0) as costs'))
        //         ->whereBetween('v.date',[$firstDayofYear, $thisDay])
        //         ->where('ca.sub_account_type_id', 8)
        //         // ->orWhere('ca.sub_account_type_id', 8)
        //         ->orderBy('v.date', 'asc')
        //         ->groupBy(DB::raw('DATE_FORMAT(v.date, "%b")'))
        //         ->get();
            // dd($costs);

        $ca_expenses = DB::table('chart_accounts as ca')
                ->join('vouchers as v', 'ca.id','=','v.chart_account_id')
                ->select(DB::raw('DATE_FORMAT(v.date, "%b") as month'),
                        DB::raw('IFNULL(SUM(debit-credit), 0) as ca_expenses'))
                ->whereBetween('v.date',[$firstDayofYear, $thisDay])
                ->where('ca.account_type_id', 9)
                ->orderBy('v.date', 'asc')
                ->groupBy(DB::raw('DATE_FORMAT(v.date, "%b")'))
                ->get();

        /*$cms_expense = DB::table('c_m_s_expenses')
                ->select(DB::raw('DATE_FORMAT(date, "%b") as month'), 
                        DB::raw('IFNULL(SUM(amount), 0) as cms_expense'))
                ->whereBetween('date',[$firstDayofYear, $thisDay])
                ->orderBy('date', 'asc')
                ->groupBy(DB::raw('DATE_FORMAT(date, "%b")'))
                ->get();*/

        $taxes = DB::table('chart_accounts as ca')
                ->join('vouchers as v', 'ca.id','=','v.chart_account_id')
                ->select(DB::raw('DATE_FORMAT(v.date, "%b") as month'),
                        DB::raw('IFNULL(SUM(debit), 0) as taxes'))
                ->whereBetween('v.date',[$firstDayofYear, $thisDay])
                ->where('v.tax_id','>',0)
                ->where('v.key' , '=' ,'tax_debit')
                ->orderBy('v.date', 'asc')
                ->groupBy(DB::raw('DATE_FORMAT(v.date, "%b")'))
                ->get();
				
        for($i=$dateFrom-1;$i<$dateTo;$i++){
            
            if(!empty($cms_dp)){
                if($cms_d<count($cms_dp) && $month[$i]==$cms_dp[$cms_d]->month) {
                    $cms_dp_arr[] = $cms_dp[$cms_d]->downpayment;
                    $cms_d++;
                }else {
                    $cms_dp_arr[] = 0;
                }
            }else{
                $cms_dp_arr[] = 0;
            }

            if(!empty($cms_ar)){
                if($cms_a<count($cms_ar) && $month[$i]==$cms_ar[$cms_a]->month) {
                    $cms_ar_arr[] = number_format($cms_ar[$cms_a]->ar,2);
                    // $cms_ar_arr[] = 'test';
                    $cms_a++;
                }else {
                    $cms_ar_arr[] = 0;
                }
            }else{
                $cms_ar_arr[] = 0;
            }
            
            if(!empty($costs)) {
                if($c<count($costs) && $month[$i]==$costs[$c]->month) {
                    $cost_arr[] = $costs[$c]->costs;
                    $c++;
                }else {
                    $cost_arr[] = 0;
                }
            }else {
                $cost_arr[] = 0;
            }

            if(!empty($aw_costs)) {
                if($cms_aw<count($aw_costs) && $month[$i]==$aw_costs[$cms_aw]->month) {
                    $cms_aw_cost_arr[] = $aw_costs[$cms_aw]->aw_costs;
                    $c++;
                }else {
                    $cms_aw_cost_arr[] = 0;
                }
            }else {
                $cms_aw_cost_arr[] = 0;
            }

            if(!empty($ca_expenses)) {
                if($ce<count($ca_expenses) && $month[$i]==$ca_expenses[$ce]->month) {
                    $ca_expense_arr[] = $ca_expenses[$ce]->ca_expenses;
                    $ce++;
                }else {
                    $ca_expense_arr[] = 0;
                }
            }else {
                $ca_expense_arr[] = 0;
            }

            if(!empty($cms_expense)) {
                if($cms_e<count($cms_expense) && $month[$i]==$cms_expense[$cms_e]->month) {
                    $cms_expense_arr[] = $cms_expense[$cms_e]->cms_expense;
                    $cms_e++;
                }else {
                    $cms_expense_arr[] = 0;
                }
            }else {
                $cms_expense_arr[] = 0;
            }

            if(!empty($taxes)) {
                if($t<count($taxes) && $month[$i]==$taxes[$t]->month) {
                    $tax_arr[] = $taxes[$t]->taxes;
                    $t++;
                }else {
                    $tax_arr[] = 0;
                }
            }else {
                $tax_arr[] = 0;
            }
        }
        
        return $charts[] = (object) [
            'cms_dp' => $cms_dp_arr,
            'cms_ar' => $cms_ar_arr,
            'aw_costs' => $cms_aw_cost_arr,
            'costs' => $cost_arr,
            'taxes' => $tax_arr,
            'ca_expenses' => $ca_expense_arr,
            'cms_expense' => $cms_expense_arr,
        ];
        //'2018-01-01 00:00:00' AND '2018-01-31 12:59:59'
    }

}

