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
                <h1>Project Billing Invoice <a href="{{ url('construction-monitoring-system/project-list') }}" class="btn btn-primary btn-xs" target="_blank" title="Create billing invoice"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>
            </div>
        </div>
        <div class="col-lg-10">
            <div class="row">
                {!! Form::open(array('url' => 'construction-monitoring-billing/search','method'=>'GET')) !!}
                <div class="col-md-7  align-right">
                    <input type="text" name="search_billing_invoice" class="search-billing form-control" placeholder="Search by customer or by project name/type or by status('Unpaid or paid') or by invoice no."/>
                    {{-- <input type="hidden" name="project_id" value="{{$cms_projects->id}}"> --}}
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
                    <th class="right">date</th>
                    <th>Invoice No</th>
                    <th>Customer</th>
                    <th>Project Name</th>
                    <th class="right">Amount</th>
                    <th>Status</th>
                    <th>Account Receivable</th>
                    <th class="actions">Action</th>
                </tr>
            @foreach ($cms_billing as $key => $item)

            <tr>
                <td>{{ $cms_billing->firstItem() + $key }}</td>
                <td class="right">{{ $item->date }}</td>
                <td>{{ $item->billing_invoice_no }}</td>
                <td>@if($item->individual == 0){{ $item->company_name }}@else{{ $item->last_name }}, {{ $item->first_name }}, {{ $item->middle_name }}@endif</td>
                <td>{{ $item->name }} - {{ $item->type }}</td>
                <td class="right">{{ number_format($item->amount, 2) }}</td>
                <td>{{ $item->status }}</td>
                <td>@if($item->status == 'Unpaid')<a class="btn btn-success btn-xs" href="{{ url('construction-monitoring-ar/create-ar', $item->id) }}" target="_blank" title="Create Account Receivable"><i class="fa fa-list-alt"></i> Create A/R</a>@endif</td>
                <td>
                    @if($item->status == 'Unpaid')
                    <a class="btn btn-primary btn-xs" href="{{ route('construction-monitoring-billing.edit', $item->id) }}" title="Edit Billing"><i class="fa fa-pencil"></i></a>
                    {!! Form::open(['method' => 'DELETE','route' => ['construction-monitoring-billing.destroy', $item->id],'style'=>'display:inline']) !!}
                    {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Billing Invoice',
                                    'onclick'=>'return confirm("Confirm delete?")'
                    )); !!}
                    {!! Form::close() !!}
                    @endif
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="5" class="right">Total</td>
                <td class="right">{{ number_format($billing_amount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" class="right"><strong>Overall Total</strong></td>
                <td class="right"><strong>{{ number_format($total_billing, 2) }}</strong></td>
            </tr>
            </table>
            <div class="row" style="margin-right: 0px; margin-left: 0px;">
                <div class="col-md-6">
                    <a class="btn btn-primary" href="{{ route('construction-monitoring-billing.index') }}"><i class="fa fa-refresh"></i> Refresh</a>
                </div>
                <div class="col-md-6" style="text-align: right;">
                    <div class="pagination" style="margin:0;">{!! $cms_billing->appends(['search_billing_invoice' => $search_billing_invoice, 'date_from' => $date_from, 'date_to' => $date_to])->render(); !!}</div>
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

    $( ".search-billing" ).focus();

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
</style>
@endsection