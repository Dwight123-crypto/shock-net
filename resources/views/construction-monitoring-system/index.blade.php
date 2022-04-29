@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection

@section('content')
<div class="content-wrapper">

    <section class="content-header">
        <div class="row">
            <div class="col-md-6">
                @include('_includes.message')
                <h1 style="color: #20292D; margin-top: 0;">Construction Monitoring Dashboard</h1>
            </div>
            <div class="col-md-6" style="text-align: right;">
                <a href="{{ url('/construction-monitoring-system') }}" class="btn btn-primary">Refresh CMS Dashboard</a>
            </div>
        </div>
    </section>
    
    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-employee warning-msg-true">
        <p>{{ $message }}</p>
    </div>
    @endif
    <!-- Main content -->
    <section class="search-container">
        
    </section>
    <section class="content">
    {{-- */
        $x=0;
    /* --}}
        <div class="row wfm-dashboard">
            <div class="row search-date">
                <div class="col-lg-7 dateRange-result">
                    <div class="row">
                        <div class="col-md-12">
                            @if(empty($startDate))
                            <h3>Date Range Result: N/A</h3>
                            @else
                            <h3>Date Range Result: {{ date('F d, Y', strtotime($startDate)) }} to {{date('F d, Y', strtotime($endDate))}}</h3>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-5 col-sm-12 dateRange">
                    {!! Form::open(array('url' => 'construction-monitoring-system/dashboard-search','method'=>'GET')) !!}
                        <span id="date_range"><input type="text" name="date_range" class="form-control date-range-search" placeholder="Date Range"></span>
                        <button Class="btn btn-primary btn-search-date">
                            <i class="fa fa-search"></i> <span>SEARCH</span>
                        </button>
                    {!! Form::close() !!}
                </div>
            </div>

            <div class="col-lg-4">
                <div class="col-lg-12" style="background-color: #D2691E">
                    <h3>TOTAL PROJECTS COST</h3>
                    <h2>{{ number_format($projectCost, 2) }}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12" style="background-color: #8196a9">
                    <h3>TOTAL ADDITIONAL WORK COST </h3>
                    <h2>{{ number_format($projAdditionalWorkCost, 2) }}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12 expenses-today">
                    <h3>TOTAL NO OR EXPENSES TODAY<br>OR SELECTED DATE RANGE</h3>
                    <h2>{{ number_format($totalNoORExpensesToday, 2) }}</h2>
                    {{-- <a href="{{ route('pos-expenses.create') }}" class="btn regpro-btn btn-primary">Add Expenses</a>
                    <a href="{{ url('pos-expenses/tracks-expenses') }}" class="btn regpro-btn btn-primary">Track Expenses</a> --}}
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12" style="background-color: #750775">
                    <h3>GROSS INCOME</h3>
                    <h2>{{ number_format($grossIncome, 2) }}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12 sap-filter" style="background-color: #FFA07A;">
                    <h3>NET INCOME</h3>
                    <h2>{{ number_format($netIncome, 2) }}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12 non-tax-sales-today">
                    <h3>TOTAL NON-TAX INCOME TODAY<br>OR SELECTED DATE RANGE</h3>
                    <h2>{{ number_format($totalNonTaxIncomeToday, 2) }}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12" style="background-color: #FDB8C5">
                    <h3>BILLING INVOICE</h3>
                    <h2>{{ number_format($cmsBilling, 2) }}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12" style="background-color: #3C8DBC">
                    <h3>ACCOUNT RECEIVABLE</h3>
                    <h2>{{ number_format($cmsAR, 2) }}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-lg-12" style="background-color: #14A65B">
                    <h3>NON-TAX INCOME THIS MONTH<br>OR SELECTED DATE RANGE</h3>
                    <h2>{{ number_format($incomeCurrentMonth, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="trans-summary row">
            <div class="col-lg-4">
                <div class="col-md-12 summary-box">
                    <h4>Income Transaction Summary</h4>
                    <table class="table table-hover" style="width:100%;">
                        <tr class="data-row">
                            <th style="text-align: left;">Income</th>
                            <th style="text-align: right;">Count</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                        <tr class="data-row">
                            <td>Today</td>
                            <td align="right">@if(!empty($today_trans_count)){{$today_trans_count}}@else 0 @endif</td>
                            <td align="right">@if(!empty($today_trans)){{number_format($today_trans,2)}}@else 0.00 @endif</td>
                        </tr>
                        <tr class="data-row">
                            <td>Yesterday</td>
                            <td align="right">@if(!empty($yesterday_trans_count)){{$yesterday_trans_count}}@else 0 @endif</td>
                            <td align="right">@if(!empty($yesterday_trans)){{number_format($yesterday_trans,2)}}@else 0.00 @endif</td>
                        </tr>
                        <tr class="data-row">
                            <td>Week</td>
                            <td align="right">@if(!empty($week_trans_count)){{$week_trans_count}}@else 0 @endif</td>
                            <td align="right">@if(!empty($week_trans)){{number_format($week_trans,2)}}@else 0.00 @endif</td>
                        </tr>
                        <tr class="data-row">
                            <td>Month</td>
                            <td align="right">@if(!empty($month_trans_count)){{$month_trans_count}}@else 0 @endif</td>
                            <td align="right">@if(!empty($month_trans)){{number_format($month_trans,2)}}@else 0.00 @endif</td>
                        </tr>
                        <tr class="data-row">
                            <td>Year</td>
                            <td align="right">@if(!empty($year_trans_count)){{$year_trans_count}}@else 0 @endif</td>
                            <td align="right">@if(!empty($year_trans)){{number_format($year_trans,2)}}@else 0.00 @endif</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="col-md-12 summary-box">
                    <h4>Top 5 Customers</h4>
                    <table class="table table-hover" style="width:100%;">
                        <tr class="data-row">
                            <th style="text-align: left;">Customer Name</th>
                            <th style="text-align: right;">Project Count</th>
                            <th style="text-align: right;">Total Cost</th>
                        </tr>
                        @foreach($top_5_customers as $top_5_customer)
                        <tr class="data-row">
                            <td align="left">
                                @if($top_5_customer->individual == 1)
                                {{$top_5_customer->first_name}} {{$top_5_customer->middle_name}} {{$top_5_customer->last_name}}
                                @else
                                {{$top_5_customer->company_name}}
                                @endif
                            </td>
                            <td align="right">{{$top_5_customer->project_count}}</td>
                            <td align="right">{{number_format($top_5_customer->total_cost,2)}}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="trans-summary row" style="margin-top: 0px">
            <div class="col-lg-12">
                <div class="col-md-12 summary-box">
                    <h4>5 Recent Income</h4>
                    <table class="table table-hover" style="width:100%;">
                        <tr class="data-row">
                            <th style="text-align: left;">Customer Name</th>
                            <th style="text-align: left;">Project Name</th>
                            <th style="text-align: right;">Date</th>
                            <th style="text-align: right;">Downpayment</th>
                            <th style="text-align: right;">A/R Amount</th>
                        </tr>
                        @foreach($cms_ar as $recent_5_income)
                        <tr class="data-row">
                            <td align="left">
                                @if($recent_5_income->individual == 1)
                                {{$recent_5_income->first_name}} {{$recent_5_income->middle_name}} {{$recent_5_income->last_name}}
                                @else
                                {{$recent_5_income->company_name}}
                                @endif
                            </td>
                            <td>{{$recent_5_income->name}}</td>
                            <td align="right">{{$recent_5_income->date}}</td>
                            <td align="right">{{number_format($recent_5_income->downpayment,2)}}</td>
                            <td align="right">{{number_format($recent_5_income->amount,2)}}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
@endsection

@section('footer_script_preload')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endsection

@section('footer_script')

<style type="text/css">
    div.wfm-dashboard{
        margin-right:0; margin-left:0; border: 1px solid #A9A9A9; padding-bottom: 15px; padding-left: 15px; border-radius: 5px; padding-right: 15px;
    }
    div.wfm-dashboard h2{
        font-size: 50px;
    }
    div.wfm-dashboard div.col-lg-12{
    min-height: 210px;
    padding-bottom: 10px;
    max-height: 210px;
    padding-top: 1px;
    }
    div.search-date{
        padding: 15px 0;
        margin-right: auto!important;
        margin-left: auto!important;
        border: 1px solid #A9A9A9;
        margin-top: 20px;
        border-radius: 5px;
    }
    div.search-date .dateRange{
        float: right;
        text-align: right;
    }
    .search-container{
        margin-right: 0;
        margin-left: 0;
        padding: 15px;
    }
    .date-range-search{
        max-width: 350px;
        display: inline-block!important;
        vertical-align: middle;
        cursor: default;
        font-size: 18px;
    }
    .daterangepicker.ltr .ranges {
        float: right!important;
    }
    .daterangepicker.show-ranges .drp-calendar.left {
        /*border-left: 0px solid #ddd!important;*/
        border-left: 0px!important;
    }
    .daterangepicker.ltr .drp-calendar.right {
        border-right: 1px solid #ddd!important;
    }
    .dateRange-result h3{
        margin-top: 0;
        margin-bottom: 0;
        color: #667EA3;
    }
    .trans-summary{
        margin-right: auto!important;
        margin-left: auto!important;
        margin-top: 20px;
    }
    .trans-summary .col-lg-4, .trans-summary .col-lg-8{
        margin: 20px 0;
    }
    .summary-box{
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 10px;
        overflow-x: auto;
        min-height: 284px;
    }
    .summary-box h4{
        color: #000;
        text-align: left;
    }
    table {
        border: 0px solid #f4f4f4;
        color: #000;
    }
    .data-row td, .data-row th{
        border: none!important;
        border-bottom: 1px solid #ccc!important;
    }
    .data-row th{
        border-top: 1px solid #ccc!important;
    }
    div.wfm-dashboard div.col-lg-4 {
        padding: 0px 8px;
    }
    
    /*Pop up css*/
    td.expired{
        color: red;
        font-weight: bold;
    }
    #outofStocksLabel{
        font-weight: bold;
    }
    .modal-body{
        overflow-x: auto;
    }

    @media (min-width: 768px){
        .modal-dialog {
            width: 1100px;
        }
    }
</style>

<script type="text/javascript">

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end){
        $('.date-range-search').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#date_range').daterangepicker({
    "alwaysShowCalendars": true,
    "opens": "left",
    startDate: start,
    endDate: end,
    ranges: {
       'Today': [moment(), moment()],
       'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
       'Last 7 Days': [moment().subtract(6, 'days'), moment()],
       'Last 30 Days': [moment().subtract(29, 'days'), moment()],
       'This Month': [moment().startOf('month'), moment().endOf('month')],
       'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);
    cb(start, end);

    // Popup script code
    $(document).ready(function(){

        var rowCount = $('.outofstock-table tr.count').length;

        if(rowCount >= 1){
           $("#outofStocks").modal('show');
        }else{
            $("#outofStocks").modal('hide');
        }
    });
</script>
@endsection