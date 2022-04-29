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
        <div class="col-lg-3 margin-tb">
            <div class="pull-none">
                <h1>Details of project expenses</h1>
                <h4>Project: ({{$cms_projects->id}}) - {{$cms_projects->name}} - {{ucfirst(trans($cms_projects->type))}}</h4>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="row">
                {!! Form::open(array('url' => 'construction-monitoring-expenses/details/search-details','method'=>'GET')) !!}
                <div class="col-md-7  align-right">
                    <input type="text" name="search_expenses_details" class="search-expenses form-control" placeholder="Search by vendor/supplier or by description or by invoice no."/>
                    <input type="hidden" name="project_id" value="{{$cms_projects->id}}">
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
                    <th>Invoice No.</th>
                    <th>Vendor/Supplier</th>
                    <th>Terms</th>
                    <th>Period</th> 
                    <th class="right">Amount</th>
                    <th style="width: 350px;">Description</th>
                    <th class="actions">Action</th>
                </tr>
            @foreach ($details as $key => $item)

            <tr>
                <td>{{ $details->firstItem() + $key }}</td>
                <td class="right">{{ date('m/d/Y', strtotime($item->date)) }}</td>
                <td>{{ $item->invoice_no }}</td>
                <td>@if($item->individual == 1) {{ $item->last_name }}, {{ $item->first_name }}, {{ $item->middle_name }}@else{{ $item->company_name }} @endif</td>
                <td>{{ $item->terms }}</td>
                <td>{{ $item->period }}</td>
                <td class="right">{{ number_format($item->amount, 2) }}</td>
                <td>{{ $item->description }}</td>
                <td>
                    <a class="btn btn-primary btn-xs" href="{{ route('construction-monitoring-expenses.edit', $item->id) }}" title="Edit Expenses"><i class="fa fa-pencil"></i></a>
                    {!! Form::open(['method' => 'DELETE','route' => ['construction-monitoring-expenses.destroy', $item->id],'style'=>'display:inline']) !!}
                    {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Expenses',
                                    'onclick'=>'return confirm("Confirm delete?")'
                    )); !!}
                    {!! Form::close() !!}
                </td>
            </tr>
            @endforeach
            </table>
            <div class="row" style="margin-right: 0px; margin-left: 0px;">
                <div class="col-md-2">
                    <a class="btn btn-primary" href="{{ url('construction-monitoring-expenses') }}"> Back</a>
                    <a class="btn btn-primary" href="{{ url('construction-monitoring-expenses/details', $cms_projects->id) }}"> Refresh</a>
                </div>
                <div class="col-md-10" style="text-align: right;">
                    <div class="pagination" style="margin:0;">{!! $details->appends(['search_expenses_details' => $search_expenses_details])->render(); !!}</div>
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
        width: 130px;
    }
    .pull-none h1{
        font-size: 24px;
        margin-top: 10px;
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