@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection

@section('header_style_postload')
<link rel="stylesheet" href="{{ url('/') }}/assets/plugins/iCheck/square/blue.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		@include('_includes.message')
		<div class="pull-left">
      <h1>
        Edit Expenses ({{ $cms_expenses->id }})
      </h1>
		</div>
    <div class="pull-right">
      <a class="btn btn-primary" href="{{ url('construction-monitoring-expenses/details', $cms_expenses->project_id) }}"> Back</a>
    </div>
    <div style="clear: both;"></div>
	</section>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-product">
            <p>{{ $message }}</p>
        </div>
    @endif

	<!-- Main content -->
	<section class="content">

  {!! Form::model($cms_expenses, ['method' => 'PATCH','route' => ['construction-monitoring-expenses.update', $cms_expenses->id]]) !!}
<div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"> &nbsp; </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
            
            <div class="col-sm-6">
              <div class="input-group input-group-sm">
                <div class="input-group-btn"><button type="button" class="btn">Vendor</button></div>
                <!-- /btn-group -->
                <input type="text" class="form-control vendor_name" value="{{$cms_expenses->company_name}}">
                {!! Form::hidden('vendor_id', null, ['class' => 'form-control']) !!}
                <span class="input-group-btn"><button type="button" class="btn btn-info btn-flat btn-search">Go!</button></span>
              </div>
              <button type="button" class="btn btn-info btn-flat vendor-search_by_id hidden" data-vendor_id="{{ $cms_expenses->vendor_id }}">Hidden vendor search by id</button>
              
              <div class="box box-info box-solid vendor-info-box">
                <div class="box-header with-border">
                  <h3 class="box-title text-sm">Vendor Info</h3>
                  <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button></div>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="box-body table-responsive no-padding">
                    <table class="table table-hover table-vendor-info"><tbody>
                      <tr class="individual_name"><th>Full Name</th><td class="name"></td></tr>
                      <tr class="company_name hidden"><th>Company Name</th><td class="name"></td></tr>
                      <tr><th>Individual?</th><td class="individual"></td></tr>
                      <tr><th>City</th><td class="city"></td></tr>
                      <tr><th>Country</th><td class="country"></td></tr>
                      <tr><th>TIN</th><td class="tin"></td></tr>
                      <tr><th>Branch Code</th><td class="branch_code"></td></tr>
                      <tr><th>Opening Balance</th><td class="opening_balance"></td></tr>
                      <tr><th>Phone</th><td class="phone"></td></tr>
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
                <div class="input-group-btn">
                  <button type="button" class="btn">Date</button>
                </div>
                <!-- /btn-group -->
                {!! Form::text('date', null, array('placeholder' => 'Date','class' => 'form-control datepicker font-sizing')) !!}
              </div>
              <div class="input-group input-group-sm">
                <div class="input-group-btn">
                  <button type="button" class="btn">Invoice No.</button>
                </div>
                <!-- /btn-group -->
                {!! Form::text('invoice_no', null, array('placeholder' => 'Invoice No','class' => 'form-control font-sizing')) !!}
              </div>
              <div class="input-group input-group-sm expenses">
                <div class="input-group-btn">
                  <button type="button" name="" class="btn">Type of Expenses</button>
                </div>
                <!-- /btn-group -->
                {{-- <select name="type_of_expenses" class="form-control type_of_expenses">
                  <option value="admin expenses">Admin Expenses</option>
                  <option value="project expenses">Project Expenses</option>
                </select> --}}
                {!! Form::select('expenses_type', ['admin expenses'=>'Admin Expenses', 'project expenses'=>'Project Expenses'], null, ['class' => 'form-control expenses_type']) !!}
              </div>
              <div class="input-group input-group-sm project-list">
                <div class="input-group-btn">
                  <button type="button" name="" class="btn">Project List</button>
                </div>
                <!-- /btn-group -->
                <select name="project_id" class="form-control">
                  <option value="{{$cms_expenses->project_id}}">{{$cms_expenses->name}} - {{$cms_expenses->type}}</option>
                  @foreach ($cms_projects as $item)
                    <option value="{{$item->id}}">{{$item->name}} - {{ ucfirst(trans($item->type)) }}</option>
                  @endforeach
                </select>
              </div>
              <div class="input-group input-group-sm terms">
                <div class="input-group-btn">
                  <button type="button" name="" class="btn">Terms</button>
                </div>
                <!-- /btn-group -->
                {!! Form::select('terms', ['cod'=>'COD', 'On account'=>'On account'], null, ['class' => 'form-control']) !!}
              </div>
              <div class="input-group input-group-sm period terms-ch terms-on-account">
                <div class="input-group-btn">
                  <button type="button" name="" class="btn"></button>
                </div>
                <!-- /btn-group -->
                {!! Form::select('period', [''=>'Select', '30'=>'30 days', '45'=>'45 days', '180'=>'180 days'], null, ['class' => 'form-control']) !!}
              </div>
              <div class="input-group input-group-sm description">
                <div class="input-group-btn">
                  <button type="button" class="btn">Desc / Memo</button>
                </div>
                <!-- /btn-group -->
                {!! Form::text('description', null, array('placeholder' => 'Description','class' => 'form-control')) !!}
              </div>
              <div class="input-group input-group-sm">
                <div class="input-group-btn">
                  <button type="button" class="btn">Amount</button>
                </div>
                <!-- /btn-group -->
                {!! Form::text('amount', null, array('placeholder' => 'Amount','class' => 'form-control font-sizing amount')) !!}
              </div>
            </div>
            
            </div>
            <!-- /.row -->
        </div>
        <!-- ./box-body -->
    </div>

    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                <a href="{{ route('construction-monitoring-system.index') }}" class="btn btn-primary" style="float: left; margin-right: 10px;">Back to CMS Dashboard</a>
                {!! Form::submit('Update', ['class' => 'btn btn-primary form-control', 'style'=>'width:100px;']) !!}
            </div>
        </div>
    </div>
	{!! Form::close() !!}

	</section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection


@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('footer_script')
<script>
    $('.datepicker').datepicker({
        autoclose: true,
        todayBtn: "linked",
        todayHighlight: true,
        format: 'yyyy-mm-dd'
    }).datepicker('update');
    
    /* Terms Dropdown */
    $('[name=terms]').change(function(){
        $('.terms-ch').hide();
        if($(this).val() == 'cod'){
            $('.terms-cod').css("display", "table");
        }
        else if($(this).val() == 'On account'){
            $('.terms-on-account').css("display", "table");
        }
        //cpv_show_hide_account_details();
    });
    $('[name=terms]').trigger('change');
    
    $('.vendor_name').autocomplete({
        source: '{{ url("construction-monitoring-expenses/find-vendors") }}',
        minLength: 3
    });

    /* Search for a vendor info. */
    $('.btn-search, .vendor-search_by_id').on('click', function(){
        var s = ($('.vendor_name').val()).trim();
        var request = { s: s };
        
        if( s && $('.vendor_name').data('ui-autocomplete') && $('.vendor_name').data('ui-autocomplete').selectedItem ){
            request.vendor_id = $('.vendor_name').data('ui-autocomplete').selectedItem.id;
        }
        else if( !s ){
            // alert('it\'s empty'); 
            return;
        }
        console.log( request );
        
        $('.box .overlay').removeClass('hidden');
        
        $.get('{{url("construction-monitoring-expenses/find-vendor")}}', request, function(data){
            if(data && Object.keys(data).length){
                for(var prop in data){
                    if(prop == 'individual' && data[prop] == '1'){
                        $('.table-vendor-info td.'+prop).html( '<span class="badge bg-green">Yes</span>' );
                        $('.table-vendor-info tr.individual_name').removeClass( 'hidden' );
                        $('.table-vendor-info tr.company_name').addClass( 'hidden' );
                    }
                    else if(prop == 'individual' && data[prop] == '0'){
                        $('.table-vendor-info td.'+prop).html( '<span class="badge bg-yellow">No</span>' );
                        $('.table-vendor-info tr.company_name').removeClass( 'hidden' );
                        $('.table-vendor-info tr.individual_name').addClass( 'hidden' );
                    }
                    else if(prop == 'id'){
                        $('input[name=vendor_id]').val( data[prop] );
                    }
                    else{
                        $('.table-vendor-info td.'+prop).html( data[prop] );
                    }
                }
            }
            else{
                $('.table-vendor-info td').html('');
                $('input[name=vendor_id]').val('');
            }
        }, 'json').always(function(){
            $('.box .overlay').addClass('hidden');
        });
    });
    
    /* Search Vendor by ID */
    $('.vendor-search_by_id').trigger('click');

    $(".amount").change(function() {
      $(".amount").val(accounting.formatMoney($('.amount').val()));
    });

    $( document ).ready(function() {

      $(".amount").val(accounting.formatMoney($('.amount').val()));

      $('.amount').click( function( event_details ) {
        $(this).select();
      });

      $(".project-list").hide();
      if($(".expenses_type").val() == 'project expenses'){
        $(".project-list").show();
      }

      $(".expenses_type").change(function() {
        if($(this).val() == 'project expenses'){
          $(".project-list").show();
        }else if($(this).val() == 'admin expenses'){
          $(".project-list").hide();
        }
      });
    });

    // $('[name=expenses_type]').trigger('change');
</script>
<style>
  input.font-sizing{font-size: 25px!important;}
  button.btn{width: 110px;text-align: left;}
  .btn-search{width: unset!important;}
  .btn-box-tool{width: unset!important;}
</style>
@endsection