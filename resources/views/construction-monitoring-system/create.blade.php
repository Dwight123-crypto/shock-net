@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_preload')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection


@section('header_style_postload')
<style>
#tbl-items .actions { width: 24px; }
#tbl-items .fa-close { opacity: 0.5; }
#tbl-items .fa-close:hover, #tbl-items .fa-close:focus { opacity: 1; }
#tbl-items .fa-close.text-red:active { color: #b52e1e !important; } /* dark red */
.datepicker{font-size: 18px!important;}
.save-project-entry{width: 150px;}
.f-label{width: 150px;}
.alert{margin: 10px 15px 0;}
.table-account-details tr.optional { display: none; }
button.btn{width: 110px;text-align: left;}
.btn-search{width: unset!important;}
.btn-box-tool{width: unset!important;}
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <div class="pull-left">
            <h1>New Project Entry</h1>
        </div>
        
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ url('construction-monitoring-system/project-list') }}"> Back</a>
        </div>
        <div style="clear: both;"></div>
        
    </section>

    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-cmsproject warning-msg-true">
        <p>{{ $message }}</p>
    </div>
    @endif

    @if ($message = Session::get('warning'))
    <div class="alert alert-warning alert-cmsproject warning-msg-true">
        <p>{{ $message }}</p>
    </div>
    @endif

    <!-- Main content -->
    <section class="content">
    
    {!! Form::open(array('route' => 'construction-monitoring-system.store','method'=>'POST')) !!}
    
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"></h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                
            <div class="col-sm-6">
                <div class="input-group input-group-sm">
                    <div class="input-group-btn"><button type="button" class="btn">Customer</button></div>
                    <input type="text" size="250" class="form-control customer_name" name="customer_name" id="cust_ajax" value="" placeholder="Customer Name" autocomplete="off">
                    <input type="hidden" name="customer_id" class="id" value="">
                    
                    <span class="input-group-btn"><button type="button" class="btn btn-info btn-flat btn-search">Go!</button></span>
                </div>
        
                <div class="box box-info box-solid customer-info-box">
                    <div class="box-header with-border">
                        <h3 class="box-title text-sm">Customer Info</h3>
                        <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button></div>
                    </div>
                    
                    <div class="box-body">
                        <div class="box-body table-responsive no-padding">
                            <input type="hidden" name="full_name" class="full_name" value="">
                            <table class="table table-hover table-customer-info"><tbody>
                                <tr class="individual_name"><th>Name</th><td class="full_name"></td></tr>
                                <tr><th>Individual?</th><td class="individual"></td></tr>
                                <tr><th>City</th><td class="city"></td></tr>
                                <tr><th>Country</th><td class="country"></td></tr>
                                <tr><th>TIN</th><td class="tin"></td></tr>
                                <tr><th>Branch Code</th><td class="branch_code"></td></tr>
                                <tr><th>Phone</th><td class="phone_no"></td></tr>
                                <tr><th>Fax</th><td class="fax"></td></tr>
                                <tr><th>Email</th><td class="email"></td></tr>
                            </tbody></table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="overlay hidden">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </div>
            </div>
                    
            <div class="col-sm-6 right-info">
                <div class="input-group input-group-sm" style="margin-bottom: 10px; width: 100%;">
                    <div class="input-group-btn"><button type="button" class="btn">Date</button></div>
                    <input type="text" class="form-control datepicker" name="date">
                </div>
            </div>
            
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title title">Project Entry</h3>
            <h3 class="box-title err-title"> &nbsp; </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div style="clear: both;"></div>
            <div class="row">
                <div class="col-md-9 col-sm-12">
                    <table class="table no-border">
                        <tbody>
                            <tr class="type-row">
                            <td class="f-label">Project Type</td>
                            <td class="field-only">
                                <select name="type" class="form-control" required>
                                    <option>Select project type</option>
                                    <option value="Build">Build</option>
                                    <option value="Design">Design</option>
                                </select>
                            </td>
                            </tr>
                            <tr class="name-row">
                                <td class="f-label">Project Name</td>
                                <td class="field-only">{!! Form::text('name', null, ['class' => 'form-control project-name', 'autocomplete' => 'off', 'required']) !!}</td>
                            </tr>
                            <tr class="cost-row">
                                <td class="f-label">Project Cost</td>
                                <td class="field-only">{!! Form::text('cost', null, ['class' => 'form-control project-cost numbers-only', 'required']) !!}</td>
                            </tr>
                            <tr class="downpayment-row">
                                <td class="f-label">Downpayment</td>
                                <td class="field-only">{!! Form::text('downpayment', null, ['class' => 'form-control project-downpayment numbers-only']) !!}</td>
                            </tr>
                            <tr class="balance-row">
                                <td class="f-label">Balance</td>
                                <td class="field-only">{!! Form::text('balance', null, ['class' => 'form-control project-balance numbers-only', 'readonly']) !!}</td>
                            </tr>
                            <tr class="type-row">
                                <td class="f-label">Status</td>
                                <td class="field-only">
                                    <select name="status" class="form-control" required>
                                        <option>Select project status</option>
                                        <option value="Ongoing">Ongoing</option>
                                        <option value="Done">Done</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <div class="form-group">
                                        {!! Form::submit('Save', ['class' => 'btn btn-primary form-control save-project-entry']) !!}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    
    </section><!-- /.content -->
    
</div><!-- /.content-wrapper -->

{{-- @include('product.table') --}}

@endsection


@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection


@section('footer_script')
@include('construction-monitoring-system.form-script')  
<script type="text/javascript">
    $('.datepicker').datepicker('update', new Date());
    
    $('[name=cost]').val('0.00');
    // $('[name=amount_pay]').val('0.00');
    $('[name=downpayment]').val('0.00');
    $('[name=balance]').val('0.00');

    $( document ).ready(function() {

        $('.project-cost').click( function( event_details ) {
            $(this).select();
        });
        $('.project-downpayment').click( function( event_details ) {
            $(this).select();
        });
    });
</script>
@endsection