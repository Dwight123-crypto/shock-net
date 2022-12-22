@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('header_style_postload')
<link rel="stylesheet" href="{{ url('/') }}/assets/plugins/iCheck/square/blue.css">
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Create New Customer</h1>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('customer.index') }}"> Back</a>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {!! Form::open(array('route' => 'customer.store','method'=>'POST')) !!}
    <section class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 customer-input">
            <div class="form-group">
            <div class="col-sm-2">
                <strong>{!! Form::label('individual', 'Select Type Customer:', ['class' => 'control-label']) !!}</strong>
            </div>
            <div class="col-sm-6">
                    <div class="icheck-radio icheck-square">
                        <label><input type="radio" class="square" name="individual" value="1" checked> Individual</label>
                        <label><input type="radio" class="square" name="individual" value="0"> Non-individual</label>
                    </div>
            </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 customer-input non-individual-option hidden">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Company Name:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('company_name', null, array('placeholder' => 'Company Name','class' => 'form-control')) !!}
                </div>
            </div>
        </div>

    <div class="individual-select individual-option">
        <div class="col-xs-12 col-sm-12 col-md-12 customer-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Last Name:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('last_name', null, array('placeholder' => 'Last Name','class' => 'form-control')) !!}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 customer-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>First Name:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('first_name', null, array('placeholder' => 'First Name','class' => 'form-control')) !!}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 customer-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Middle Name:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('middle_name', null, array('placeholder' => 'Middle Name','class' => 'form-control')) !!}
                </div>
            </div>
        </div>
    </div>

        <div class="col-xs-12 col-sm-12 col-md-12 customer-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Business Name:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('business_name', null, array('placeholder' => 'Business Name','class' => 'form-control')) !!}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 customer-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Business Address:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::textarea('business_address', null, array('placeholder' => 'Business Address','class' => 'form-control')) !!}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 customer-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>City:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('city', null, array('placeholder' => 'City','class' => 'form-control')) !!}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 customer-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Country:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('country', null, array('placeholder' => 'Country','class' => 'form-control')) !!}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 customer-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>TIN:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('tin', null, array('placeholder' => 'TIN','class' => 'form-control')) !!}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 customer-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Branch Code:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('branch_code', null, array('placeholder' => 'Branch Code','class' => 'form-control')) !!}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 customer-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Phone No.:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('phone_no', null, array('placeholder' => 'Phone No.','class' => 'form-control')) !!}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 customer-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Fax:</strong>
                </div>
                <div class="col-sm-6">
                {!! Form::text('fax', null, array('placeholder' => 'Fax','class' => 'form-control')) !!}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 customer-input">
            <div class="form-group">
                <div class="col-sm-2">
                <strong>Email:</strong>
                </div>
                <div class="col-sm-6">
                    {!! Form::email('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 customer-input">
            <div class="form-group">
                <div class="col-sm-2">
                    <strong>Barcode:</strong>
                </div>
                <div class="col-sm-6">
                    <div class="col-xs-12 col-sm-12 col-md-12 text-left">
                        <div class="row">
                            <div class="col-sm-12">
                                {!! Form::text('barcode', null, array('placeholder' => 'Barcode','class' => 'form-control','id' => 'barcodeText', 'autocomplete'=>'off')) !!}
                                <img id="barcode">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Create</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </section>
        {!! Form::close() !!}
</div>

@endsection

@section('footer_script')
<script src="{{ url('/') }}/assets/plugins/iCheck/icheck.min.js"></script>
<script>
$( document ).ready(function() {
$('input[type="radio"].square, input[type="checkbox"].square').iCheck({ 
        radioClass: 'iradio_square-blue', 
        checkboxClass: 'iradio_square-blue' 
    });
    $('input').on('ifChanged', function (event) {
        if( $(event.target).attr('name') == "individual" && $(event.target).is(':checked')) {
            if( $(event.target).val() == "1" ) {
                $('.non-individual-option').addClass('hidden');
                $('.individual-option').removeClass('hidden');
            }
            else {
                $('.individual-option').addClass('hidden');
                $('.non-individual-option').removeClass('hidden');
            }
        }
    });
});

// function generateBarcode() {
$( document ).ready(function() {
    var randomNumber = Math.floor((Math.random() * 10000000000) + 1);
    $("#barcodeText").val(randomNumber);
    $("#barcodeText").attr("readonly", true);
    $("#barcodeText").focus();

    // Display the barcode
    var barcode = $("#barcodeText").val();
    if(!!barcode){
        $("#barcode").JsBarcode(barcode,{
            lineColor: "#000",
            width: 5,
            height: 250,
            fontSize: 45,
            displayValue: true
        });

        var imgsrc = $('#barcode').attr('src');
        $("a#download").attr("href", imgsrc);
        $("#barcode").css( "display", "block" );
    }
});
$(document).keyup(function(e) {
    if (e.keyCode === 27) // esc
    {
        $("#barcodeText").val("");
    }
});
</script>
<style>
#barcode{
    display: none;
    width: 100%;
    max-width: 320px;
    
}
.no-padding{
    padding: 0!important;
}
</style>
@endsection