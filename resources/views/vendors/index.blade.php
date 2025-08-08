@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('header_style_postload')
<style>
	.table th.individual { width: 90px; }
</style>
@endsection


@section('content')
<div class="content-wrapper">

	<section class="content-header">
	
		@include('_includes.message')
		<div class="row">
			<div class="col-md-2">
				<div class="pull-left">	
					<h1>
						Vendor <a href="{{ url('/vendors/create') }}" class="btn btn-primary btn-xs" title="Add New Vendor"><i class="fa fa-plus" aria-hidden="true"></i></a>
					</h1>
				</div>
			</div>
			<div class="col-md-10">
				<div class="row search-bar-billing">
                    {!! Form::open(array('url' => 'vendors/search-vendors','method'=>'GET')) !!}
                    <div class="col-md-9 col-sm-9" style="padding-right: 0px;">
                        <input type="text" name="search" class="search-vendor form-control" placeholder="Search vendor"/>
                    </div>
                    <div class="col-md-1 col-sm-1">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                    {!! Form::close() !!}
                    <div class="col-md-2 col-sm-1">
                        <!-- <a href="" @click.prevent="printAll" class="btn btn-primary" title="Print all Billing Invoice display on the screen"><i class="fa fa-print"></i> Print All</a> -->
                        <a href="{{ url('/vendors') }}" class="btn btn-primary btn-success" title="Refresh"><i class="fa fa-refresh"></i> Refresh</a>
                    </div>
                </div>
			</div>
		</div>

	</section>
	
    <!-- Main content -->
	<section class="content">
	
	<div class="table">
		<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<!-- <th>S.No</th><th> Last Name </th><th> First Name </th><th> Middle Name </th><th>Actions</th> -->
					<th>S.No</th><th> Name / Company </th><th class="individual"> Individual? </th><th class="status">Status</th><th class="actions">Actions</th>
				</tr>
			</thead>
			<tbody>
			{{-- */$x=0;/* --}}
			@foreach($vendor as $item)
				{{-- */$x++;/* --}}
				<tr>
					<td>{{ $x }}</td>
					<!-- <td>{{ $item->last_name }}</td><td>{{ $item->first_name }}</td><td>{{ $item->middle_name }}</td> --> 
					<td>{{ $item->fullname }}</td><td>@if($item->individual=='1') <span class="badge bg-green">Yes</span> @else <span class="badge bg-orange">No</span> @endif </td>				
						<td> @if($item->vendors_status == 'Inactive') Inactive @else Active @endif </td>
					<td>
						<a href="{{ url('/vendors/' . $item->id) }}" class="btn btn-success btn-xs" title="View Vendor"><i class="fa fa-eye"></i></a>
						<a href="{{ url('/vendors/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Vendor"><i class="fa fa-pencil"></i></a>
						{!! Form::open([
							'method'=>'DELETE',
							'url' => ['/vendors', $item->id],
							'style' => 'display:inline'
						]) !!}
							{!! Form::button('<i class="fa fa-trash"></i>', array(
									'type' => 'submit',
									'class' => 'btn btn-danger btn-xs',
									'title' => 'Delete Vendor',
									'onclick'=>'return confirm("Confirm delete?")'
							));!!}
						{!! Form::close() !!}
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<div class="pagination"> {!! $vendor->render() !!} </div>
	</div>

	</section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
