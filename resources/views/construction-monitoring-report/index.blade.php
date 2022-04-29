@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('header_style_preload')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-2 margin-tb">
            <div class="pull-none">
                <h1>Project Report</h1>
            </div>
        </div>
        <div class="col-lg-10">
            <div class="row">
                {!! Form::open(array('url' => 'construction-monitoring-report/search','method'=>'GET')) !!}
                <div class="col-md-7  align-right">
                    <input type="text" name="search_name_type" class="search-name-type form-control" placeholder="Search project by name or type"/>
                </div>
                <div class="col-md-3  align-right">
                    <input type="text" name="date_from" class="date-search form-control datepicker" placeholder="Date From:" autocomplete="off"/>
                    <input type="text" name="date_to" class="date-search form-control datepicker" placeholder="Date To:" autocomplete="off"/>
                </div>
                <div class="col-md-2 align-right">
                    <button type="submit" class="btn btn-primary btn-search">Search</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-product">
            <p>{{ $message }}</p>
        </div>
    @endif
    
    <section class="content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <tr>
                    <th style="width: 100px;">No</th>
                    <th>Date</th>
                    <th>Project name with type</th>
                    <th class="right">Cost</th>
                    <th class="right">Add. Work Cost</th>
                    <th class="right">Downpayment</th>
                    <th class="right">Collected Payment<br>(Downpayment + A/R)</th>
                    <th class="right">Project Expenses</th>
                    <th class="right">Gross Income</th>
                    <th class="right">Balance</th>
                    <th class="actions">Action</th>
                </tr>
            @foreach ($cm_report as $key => $item)
            <tr>
                <td>{{ $cm_report->firstItem() + $key }}</td>
                <td>{{ $item->date }}</td>
                <td>{{ $item->name }} - {{ ucfirst(trans($item->type)) }}</td>
                <td class="right">{{ number_format($item->cost, 2) }}</td>
                <td class="right">{{ number_format($item->total_aw_amount, 2) }}</td>
                <td class="right">{{ number_format($item->downpayment, 2) }}</td>
                <td class="right">{{ number_format($item->downpayment + $item->total_ar_amount, 2) }}</td> {{-- need to add account receivable from billing --}}
                <td class="right">{{ number_format($item->total_expenses, 2) }}</td>
                <td class="right">{{ number_format(($item->cost + $item->total_aw_amount) - ($item->total_expenses), 2) }}</td>
                <td class="right">{{ number_format(($item->cost + $item->total_aw_amount) - ($item->downpayment + $item->total_ar_amount), 2) }}</td> {{-- need to add account receivable from billing --}}
                <td>
                    <a class="btn btn-primary btn-xs" href="{{ url('construction-monitoring-report/details', $item->id) }}"><i class="fa fa-list"></i> Details</a>
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" class="right">Total Per page:</td>
                <td class="right">{{ number_format($total_cost, 2) }}</td>
                <td class="right">{{ number_format($total_aw_cost, 2) }}</td>
                <td class="right">{{ number_format($total_downpayment, 2) }}</td>
                <td class="right">{{ number_format($total_collected_payment, 2) }}</td>
                <td class="right">{{ number_format($total_cp_expenses, 2) }}</td>
                <td class="right">{{ number_format($total_gross_income, 2) }}</td>
                <td class="right">{{ number_format($total_balance, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3" class="right bold">Overall Total:</td>
                <td class="right bold">{{ number_format($overall_total_cost, 2) }}</td>
                <td class="right bold">{{ number_format($overall_total_aw_cost, 2) }}</td>
                <td class="right bold">{{ number_format($overall_total_downpayment, 2) }}</td>
                <td class="right bold">{{ number_format($overall_total_collected_payment, 2) }}</td>
                <td class="right bold">{{ number_format($overall_total_cp_expenses, 2) }}</td>
                <td class="right bold">{{ number_format($overall_total_gross_income, 2) }}</td>
                <td class="right bold">{{ number_format($overall_total_balance, 2) }}</td>
            </tr>
            <tr><td></td></tr>
            <tr>
                <td colspan="3" class="right bold">All Gross Income</td>
                <td class="right bold">{{ number_format($all_gross_income, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3" class="right bold">Admin Expenses</td>
                <td class="right bold">{{ number_format($all_admin_expenses, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3" class="right bold fontsize">Grand Total Net Income</td>
                <td class="right bold fontsize">{{ number_format($all_gross_income - $all_admin_expenses, 2) }}</td>
            </tr>
            </table>
            <div class="row" style="margin-right: 0px; margin-left: 0px;">
                <div class="col-md-2">
                    <a class="btn btn-primary" href="{{ url('construction-monitoring-system') }}"> Back</a>
                    <a class="btn btn-primary" href="{{ url('construction-monitoring-report') }}"><i class="fa fa-refresh"></i> Refresh</a>
                </div>
                <div class="col-md-10" style="text-align: right;">
                    <div class="pagination" style="margin:0;">{!! $cm_report->appends(['search_name_type' => $search_name_type])->render(); !!}</div>
                    {{-- <div class="pagination" style="margin:0;">{!! $cm_report->appends(['search_name_type' => $search_name_type, 'date_from' => $date_from, 'date_to' => $date_to])->render(); !!}</div> --}}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('footer_script')
<script type="text/javascript">

    $('.datepicker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
    });

    $( ".search-expenses" ).focus();

</script>

<style type="text/css">
    ul.pagination{
        margin: 0px;
    }
    .table-responsive table{
        font-size: 14px;
    }
    th.right, td.right{
        text-align: right;
        width: 150px;
    }
    .pull-none h1{
        font-size: 24px;
        margin-top: 0;
    }
    .align-right{
        margin: 5px 0;
    }
    .btn-search{
        width: 150px;
    }
    input.date-search{
        width: 49%!important;
    }
    .bold{
        font-weight: bold;
    }
    .fontsize{
        font-size: 16px;
    }
</style>
@endsection

{{-- Note:
    income = downpayment + billing(account receivable)
    profit = cost - expenses
    balance = cost - (downpayment - billing(account receivable))
--}}