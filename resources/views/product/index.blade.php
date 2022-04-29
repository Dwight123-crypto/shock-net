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
                <h1>Product List <a href="{{ route('product.create') }}" class="btn btn-primary btn-xs" title="Add New Product"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>
            </div>
        </div>
        <div class="col-lg-9">
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
                    <th>No</th>
                    <th>Barcode</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Vendors/Supplier</th>
                    <th>Expiration Date</th>
                    <th>Inventory Status</th>
                    <th class="actions">Action</th>
                </tr>
            @foreach ($products as $key => $item)

            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $item->barcode }}</td>
                <td>{{ $item->name }}</td>
                <td align="right">{{ $item->price }}</td>
                <td>{{ $item->first_name }} {{ $item->middle_name }} {{ $item->last_name }}{{ $item->company_name }}</td>
                @if($item->expiration_date <= date('Y-m-d'))
                    <td align="right" class="expired">@if($item->expiration_date == '') @else{{ date('m-d-Y', strtotime($item->expiration_date)) }}@endif</td>
                @else
                    <td align="right">@if($item->expiration_date == '') @else{{ date('m-d-Y', strtotime($item->expiration_date)) }}@endif</td>
                @endif
                <td>{{ $item->inventory_status }}</td>
                <td>
                    <a class="btn btn-success btn-xs" href="{{ route('product.show', $item->id) }}"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-primary btn-xs" href="{{ route('product.edit', $item->id) }}"><i class="fa fa-pencil"></i></a>
                    <a class="btn btn-primary btn-xs" href="{{ url('product/details', $item->id) }}" title="Stock Details"><i class="fa fa-th-list"></i></a>
                    {!! Form::open(['method' => 'DELETE','route' => ['product.destroy', $item->id],'style'=>'display:inline']) !!}
                    {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Product',
                                    'onclick'=>'return confirm("Confirm delete?")'
                    )); !!}
                    {!! Form::close() !!}
                </td>
            </tr>
            @endforeach
            </table>
            <div class="row" style="margin-right: 0px; margin-left: 0px;">
                <div class="col-md-6">
                    <div class="pagination" style="margin:0;">{!! $products->appends(['inventory_status' => $inventory_status, 'search_products' => $search_products])->render(); !!}</div>
                </div>
                <div class="col-md-6" style="text-align: right;">
                    <a href="{{ url('/product/expired-products') }}" class="btn btn-primary">Check Expired Products</a>
                    <a href="{{ url('/point-of-sale/inventory') }}" class="btn btn-primary">Go to POS stocks inventory</a>
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
    td.expired{
        color: red;
        font-weight: bold;
    }
</style>