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
                <h1>Project List <a href="{{ route('construction-monitoring-system.create') }}" class="btn btn-primary btn-xs" title="Add New Project"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>
            </div>
        </div>
        <div class="col-lg-10">
            <div class="row">
                {!! Form::open(array('url' => 'construction-monitoring-system/project-search','method'=>'GET')) !!}
                <div class="col-md-7  align-right">
                    <input type="text" name="search_projects_customers_types" class="search-projects form-control" placeholder="Search projects by Name, Type or by Customer"/>
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
                    <th style="width: 50px;">No</th>
                    <th class="right">Date</th>
                    <th>Project Name</th>
                    <th>Project Type</th>
                    <th>Customer</th>
                    <th class="right">Cost</th>
                    <th class="right">Downpayment</th>
                    <th style="width: 100px;">Status</th>
                    <th style="width: 100px;">Additional Work</th>
                    <th style="width: 100px;">Invoicing</th>
                    <th class="actions">Action</th>
                </tr>
            @foreach ($project_list as $key => $item)

            <tr>
                <td>{{ $project_list->firstItem() + $key }}</td>
                <td class="right">{{ $item->date }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ ucfirst(trans($item->type)) }}</td>
                <td>@if($item->individual == 1) {{ $item->last_name }}, {{ $item->first_name }}, {{ $item->middle_name }}@else{{ $item->company_name }} @endif</td>
                <td class="right">{{ number_format($item->cost, 2) }}</td>
                <td class="right">{{ number_format($item->downpayment, 2) }}</td>
                <td>{{ ucfirst(trans($item->status)) }}</td>
                <td><a class="btn btn-success btn-xs" href="{{ url('construction-monitoring-system/additional-work', $item->id) }}" target="_blank" title="Create additional work"><i class="fa fa-plus"></i> Additional Work</a></td>
                <td><a class="btn btn-success btn-xs" href="{{ url('construction-monitoring-billing/create-invoice', $item->id) }}" target="_blank" title="Create billing invoice"><i class="fa fa-plus"></i> Create Invoice</a></td>
                <td>
                    <a class="btn btn-primary btn-xs" href="{{ route('construction-monitoring-system.edit', $item->id) }}" title="Edit Project"><i class="fa fa-pencil"></i></a>
                    {!! Form::open(['method' => 'DELETE','route' => ['construction-monitoring-system.destroy', $item->id],'style'=>'display:inline']) !!}
                    {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Project',
                                    'onclick'=>'return confirm("This is to inform you that deleting this project will also delete all related data and it cannot be restored. Confirm delete?")'
                    )); !!}
                    {!! Form::close() !!}
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="5" class="right">Total</td>
                <td class="right">{{number_format($total_cost, 2)}}</td>
                <td class="right">{{number_format($total_downpayment, 2)}}</td>
                {{-- <td class="right">{{number_format($total_balance, 2)}}</td> --}}
            </tr>
            <tr>
                <td colspan="5" class="right"><strong>Overall Total</strong></td>
                <td class="right"><strong>{{number_format($total_project_cost, 2)}}</strong></td>
                <td class="right"><strong>{{number_format($total_project_downpayment, 2)}}</strong></td>
                {{-- <td class="right"><strong>{{number_format($total_project_balance, 2)}}</strong></td> --}}
            </tr>
            </table>
            <div class="row" style="margin-right: 0px; margin-left: 0px;">
                <div class="col-md-6">
                    <a class="btn btn-primary" href="{{ url('construction-monitoring-system/project-list') }}"><i class="fa fa-refresh"></i> Refresh</a>
                </div>
                <div class="col-md-6" style="text-align: right;">
                    <div class="pagination" style="margin:0;">{!! $project_list->appends(['search_projects_customers_types' => $search_projects_customers_types, 'date_from' => $date_from, 'date_to' => $date_to])->render(); !!}</div>
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

    $( ".search-projects" ).focus();

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