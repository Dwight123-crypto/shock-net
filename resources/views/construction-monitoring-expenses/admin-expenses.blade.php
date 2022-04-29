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
                <h1>Admin Expenses <a href="{{ route('construction-monitoring-expenses.create') }}" class="btn btn-primary btn-xs" title="Add New Expenses"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>
            </div>
        </div>
        <div class="col-lg-10">
            <div class="row">
                {!! Form::open(array('url' => 'construction-monitoring-expenses/admin-expenses/search','method'=>'GET')) !!}
                <div class="col-md-7  align-right">
                    <input type="text" name="search_vendor_description" class="search-expenses form-control" placeholder="Search by vendor/supplier or by description or by invoice no."/>
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
                    <th class="right">Date</th>
                    <th>Invoice No</th>
                    <th>Vendor/Supplier</th>
                    <th class="right">Amount</th>
                    <th>Description</th>
                    <th class="actions">Action</th>
                </tr>
            @foreach ($expenses as $key => $item)

            <tr>
                <td>{{ $expenses->firstItem() + $key }}</td>
                <td class="right">{{ $item->date }}</td>
                <td>{{ $item->invoice_no }}</td>
                <td>@if($item->individual == 0){{ $item->company_name }}@else{{ $item->last_name }}, {{ $item->first_name }}, {{ $item->middle_name }}@endif</td>
                <td class="right">{{ number_format($item->amount, 2) }}</td>
                <td>{{ $item->description }}</td>
                <td>
                    <a class="btn btn-primary btn-xs" href="{{ route('construction-monitoring-expenses.edit', $item->id) }}" title="Edit Expenses"><i class="fa fa-pencil"></i></a>
                    <a class="btn btn-danger btn-xs" href="{{ url('construction-monitoring-expenses/delete-admin-expenses', $item->id) }}" title="Delete Expenses" onclick='return confirm("Confirm delete?")'><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4" class="right">Total</td>
                <td class="right">{{ number_format($admin_expenses, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" class="right"><strong>Overall Total</strong></td>
                <td class="right"><strong>{{ number_format($overall_expenses, 2) }}</strong></td>
            </tr>
            </table>
            <div class="row" style="margin-right: 0px; margin-left: 0px;">
                <div class="col-md-2">
                    <a class="btn btn-primary" href="{{ url('construction-monitoring-system') }}"> Back</a>
                    <a class="btn btn-primary" href="{{ url('construction-monitoring-expenses/admin-expenses') }}"> Refresh</a>
                </div>
                <div class="col-md-10">
                    <div class="pagination" style="margin:0;">{!! $expenses->appends(['search_vendor_description' => $search_vendor_description, 'date_from' => $date_from, 'date_to' => $date_to])->render(); !!}</div>
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
        width: 250px;
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
    th.actions{
        width: 200px!important;
    }
</style>
@endsection