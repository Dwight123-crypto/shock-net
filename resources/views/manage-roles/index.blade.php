@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-3 margin-tb">
            <div class="pull-left">
                <h1>Roles <a href="{{ route('manage-roles.create') }}" class="btn btn-primary btn-xs" title="Add New Role"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>
            </div>
        </div>
        {{-- <div class="col-lg-9">
            <div class="row">
                <div class="col-md-6  align-right">
                    {!! Form::open(array('url' => 'product/search-product','method'=>'GET')) !!}
                    <div class="col-md-10 col-sm-12" style="padding-right: 0px;">
                        <input type="text" name="search_products" class="search-products form-control" placeholder="Search Products"/>
                    </div>
                    <div class="col-md-2 col-sm-12">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="col-md-6">
                    {!! Form::open(array('url' => 'product/search-product','method'=>'GET')) !!}
                    <div class="col-md-10 col-sm-12" style="padding-right: 0px;">
                        {!! Form::select('inventory_status', ['All'=>'All', 'Active'=>'Active', 'Inactive'=>'Inactive'], null, ['class' => 'form-control view-by-status']) !!}
                    </div>
                    <div class="col-md-2 col-sm-12">
                        <button type="submit" class="btn btn-primary">View Status</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div> --}}
        <div style="clear: both;"></div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <tr>
                    <th width="150" align="left">ID</th>
                    <th align="left">Name</th>
                    <th align="left">Permission</th>
                    <th align="left" class="actions">Action</th>
                </tr>
            @foreach ($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>{{ $role->role_name }}</td>
                <td>
                    <a class="btn btn-success btn-xs" href="{{ url('manage-permissions/set', $role->id) }}"><i class="fa fa-key"></i> Set Permission</a>
                </td>
                <td>
                    <a class="btn btn-primary btn-xs" href="{{ route('manage-roles.edit', $role->id) }}"><i class="fa fa-pencil"></i> Edit</a>
                </td>
            </tr>
            @endforeach
            </table>
            <div class="row" style="margin-right: 0px; margin-left: 0px;">
                <div class="col-md-6">
                    <div class="pagination" style="margin:0;">{!! $roles->render(); !!}</div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

<style type="text/css">
    ul.pagination{
        margin: 0px;
    }
    .table-responsive table{
        font-size: 14px;
    }
    /* .actions{
        width: 300px!important;
    } */
    .alert-success{
        margin: 15px;
    }
</style>