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
                <h1>Report Details</h1>
                <h4>Project: ({{$project->id}}) - {{$project->name}} - {{ucfirst(trans($project->type))}}</h4>
            </div>
        </div>
        {{-- <div class="col-lg-9">
            <div class="row">
                {!! Form::open(array('url' => 'construction-monitoring-expenses/details/search-details','method'=>'GET')) !!}
                <div class="col-md-7  align-right">
                    <input type="text" name="search_details" class="search-details form-control" placeholder="Search expenses by vendor/supplier"/>
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
        </div> --}}
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
                    <th>Expenses Invoice No.</th>
                    <th>A/R Official Receipt No.</th>
                    <th class="right">Project Cost</th>
                    <th class="right">Add. Work Cost</th>
                    <th class="right">Downpayment</th>
                    <th class="right">Amount</th>
                    <th class="right">Running Project Balance</th>
                    <th class="right">Running Project Expenses</th>
                </tr>

            {{-- */
                $running_balance = 0;
                $running_expenses = 0;
            /* --}}
            @foreach ($cms_ar as $key => $item)
                {{-- */
                    if(!empty($item->or_number) || empty($item->or_number) && empty($item->invoice_no)){
                        if(empty($item->or_number) && empty($item->invoice_no)){
                            $running_balance = $running_balance + (($item->cost + $item->amount) - $item->downpayment);
                        }else{
                            $running_balance = $running_balance + (($item->cost) - $item->downpayment) - $item->amount;
                        }
                    }

                    if(!empty($item->invoice_no)){
                        $running_expenses = $running_expenses + $item->amount;
                    }
                /* --}}
            <tr>
                <td>{{ $key + 1 }}</td>
                <td class="right">{{ date('m/d/Y', strtotime($item->date)) }}</td>
                <td>{{ $item->invoice_no }}</td>
                <td>{{ $item->or_number }}</td>
                <td class="right">@if($item->cost != 0){{ number_format($item->cost, 2) }}@else @endif</td>
                <td class="right">
                    @if(empty($item->or_number) && empty($item->invoice_no))
                        @if($item->cost != 0)
                        @else
                        {{ number_format($item->amount, 2) }}
                        @endif
                    @endif
                </td>
                <td class="right">@if($item->downpayment != 0){{ number_format($item->downpayment, 2) }}@else @endif</td>
                <td class="right">
                    @if(!empty($item->or_number) || !empty($item->invoice_no))
                    {{ number_format($item->amount, 2) }}
                    @endif
                </td>
                <td class="right">
                    @if(!empty($item->invoice_no)) 
                    @else
                    {{ number_format($running_balance, 2) }}
                    @endif
                </td>
                <td class="right">
                    @if(!empty($item->invoice_no)) 
                    {{ number_format($running_expenses, 2) }}
                    @else
                    @endif    
                </td>
            </tr>
            @endforeach
            </table>
            <div class="row" style="margin-right: 0px; margin-left: 0px;">
                <div class="col-md-2">
                    <a class="btn btn-primary" href="{{ url('construction-monitoring-report') }}"> Back</a>
                    {{-- <a class="btn btn-primary" href="{{ url('construction-monitoring-report/details', $project->id) }}"><i class="fa fa-refresh"></i> Refresh</a> --}}
                </div>
                {{-- <div class="col-md-10" style="text-align: right;">
                    <div class="pagination" style="margin:0;">{!! $details->appends(['search_expenses_details' => $search_expenses_details])->render(); !!}</div>
                </div> --}}
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
        width: 160px;
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