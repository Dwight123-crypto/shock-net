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

.table-account-details tr.optional { display: none; }
input.font-sizing{font-size: 25px!important;}
button.btn{width: 110px;text-align: left;}
.btn-search{width: unset!important;}
.btn-box-tool{width: unset!important;}
.customer-info-box{margin-top: 0px;}
</style>
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <div class="pull-left">
            <h1>Edit Non-OR Billing Invoice for ID: ({{ $cm_billing->id }})</h1>
        </div>
        
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('construction-monitoring-billing.index') }}"> List of billing invoice</a>
        </div>
        <div style="clear: both;"></div>
        
    </section>

    <!-- Main content -->
    <section class="content">
    
      {!! Form::model($cm_billing, ['method' => 'PATCH','route' => ['construction-monitoring-billing.update', $cm_billing->id]]) !!}
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
                <div class="input-group input-group-sm" style="display: none;">
                    <div class="input-group-btn"><button type="button" class="btn">Customer</button></div>
                    
                    <input type="text" size="250" class="form-control customer_name" name="customer_name" id="cust_ajax" value="" placeholder="Customer Name" autocomplete="off">
                    <input type="hidden" name="customer_id" class="id" value="{{ $cm_billing->customer_id }}">
                    
                    <span class="input-group-btn"><button type="button" class="btn btn-info btn-flat btn-search">Go!</button></span>
                </div>
                <button type="button" class="btn btn-info btn-flat customer-search_by_id hidden" data-customer_id="{{ $cm_billing->customer_id }}">Hidden customer search by id</button>
        
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
              <div class="input-group input-group-sm">
                <div class="input-group-btn"><button type="button" class="btn">BI No.</button></div>
                  {!! Form::text('billing_invoice_no', null, ['class' => 'form-control invoice_no font-sizing', 'readonly']) !!}
              </div>
              <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                  <div class="input-group-btn"><button type="button" class="btn">Date</button></div>
                  {!! Form::text('date', null, ['class' => 'form-control datepicker font-sizing']) !!}
              </div>
              <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                <div class="input-group-btn"><button type="button" class="btn">Project Type</button></div>
                {!! Form::text('type', null, ['class' => 'form-control type', 'readonly']) !!}
              </div>
              <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                <div class="input-group-btn"><button type="button" class="btn">Project Name</button></div>
                {!! Form::text('name', null, ['class' => 'form-control name', 'readonly']) !!}
              </div>
              <div class="input-group input-group-sm" style="margin-bottom: 10px;">
                <div class="input-group-btn"><button type="button" class="btn">Billing Amount</button></div>
                {!! Form::text('amount', null, ['class' => 'form-control amount font-sizing', 'placeholder' => 'Enter the billing amount', 'autocomplete' => 'off']) !!}
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="row">
      <div class="col-sm-3">
        <div class="form-group">
            {!! Form::submit('Update', ['class' => 'btn btn-primary form-control']) !!}
        </div>
      </div>
    </div>
    
    {!! Form::close() !!}
    
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@include('service-list.table')

@endsection


@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection


@section('footer_script')
{{-- @include('billing-invoice.form-script')   --}}
<script>
  $('.datepicker').datepicker({
        autoclose: true,
        todayBtn: "linked",
        todayHighlight: true,
        format: 'yyyy-mm-dd'
    }).datepicker('update');
  
  $('.customer_name').autocomplete({
      source: '{{ url("construction-monitoring-billing/find-customer") }}',
      minLength: 3
  });

  /* Search for a vendor info. */
  $('.btn-search, .customer-search_by_id').on('click', function(){

    var cust_name = ($('.customer_name').val()).trim();
    var request = { cust_name: cust_name };

    if( $(this).is('.customer-search_by_id') ){
        if( $(this).data('customer_id') > 0 ){
            request.customer_id = $(this).data('customer_id')
        }
        else{
            return;
        }
    }
    else if( !cust_name ){
        return;
    }
    else if( $(this).is('.btn-search') 
            && $('#cust_ajax').data('ui-autocomplete') 
            && $('#cust_ajax').data('ui-autocomplete').selectedItem ){
        request.customer_id = $('#cust_ajax').data('ui-autocomplete').selectedItem.id;
    }

    $('.box .overlay').removeClass('hidden');

    $.get('{{url("construction-monitoring-system/find-customer-only")}}', request, function(datacust){

        if(datacust && Object.keys(datacust).length){

            for(var prop in datacust){
                $('.cash-invoice-input input.' + prop).val( datacust[prop] );

                if(prop == 'individual' && datacust[prop] == '1'){
                    $('.table-customer-info td.'+prop).html( '<span class="badge bg-green">Yes</span>' );
                }
                else if(prop == 'individual' && datacust[prop] == '0'){
                    $('.table-customer-info td.'+prop).html( '<span class="badge bg-yellow">No</span>' );
                }
                else if(prop == 'id'){
                    $('input[name=customer_id]').val( datacust[prop] );
                }
                else{
                    $('.table-customer-info td.'+prop).html( datacust[prop] );
                }

                if(prop == 'full_name') $('.customer_name').val( datacust[prop] );
            
                if(prop == 'return_bottle') return_bottle = datacust[prop];
                if(prop == 'order_qty') order_qty = datacust[prop];    
            }
        }
        else{
            /* Cleanup Customer Info */
            $('input[name=customer_id], .cash-invoice-input input').val('');
            $('.table-customer-info td').html('');
        }

      }).always(function(){
          $('.box .overlay').addClass('hidden');
      });
  });
    
    /* Search Customer by ID */
    $('.customer-search_by_id').trigger('click');

    $(".amount").change(function() {
      $(".amount").val(accounting.formatMoney($('.amount').val()));
    });

    $( document ).ready(function() {
        
        $(".amount").val(accounting.formatMoney($('.amount').val()));

        $('.amount').focus();
        $('.amount').click( function( event_details ) {
            $(this).select();
        });
    });

</script>
@endsection