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
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Edit Role</h1>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('manage-roles.index') }}"> Back</a>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if (count($errors) > 0)
    <div class="col-sm-12">
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    @if ($message = Session::get('warning'))
        <div class="alert alert-warning">
            <p>{{ $message }}</p>
        </div>
    @endif

    {!! Form::model($role, ['method' => 'PATCH','route' => ['manage-roles.update', $role->id]]) !!}
<section class="content">
    <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-12 role-input">
            <div class="form-group">
                <div class="col-sm-2">
                    <strong>Role Name:</strong>
                </div>
                <div class="col-sm-6">
                    {!! Form::text('role_name', null, array('placeholder' => 'Role Name','class' => 'form-control', 'required' => 'required')) !!}
                    <div class="col-xs-12 col-sm-12 col-md-12 text-left">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
    {!! Form::close() !!}

@endsection

@section('footer_script_preload')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('footer_script')
<script>
    
</script>
<style>
    .role-input{
        background-color: #ffffff;
        padding: 15px;
    }
    .alert-warning{
        margin: 15px;
    }
</style>
@endsection