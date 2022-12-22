@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection


@section('content')
<div class="content-wrapper">

    <section class="content-header">
    
        @include('_includes.message')
        
        <h1>
            Billing Invoice <a href="{{ url('/billing-invoice/create') }}" class="btn btn-primary btn-xs" title="Add New Billing Invoice"><i class="fa fa-plus" aria-hidden="true"></i></a>
        </h1>
        
    </section>
    
    <!-- Main content -->
    <section class="content">
    
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 100px;">S.No</th>
                    <th> Pay To </th>
                    <th style="text-align: right; width: 150px;"> Date </th>
                    <th style="width: 150px;"> BI No. </th>
                    <th style="text-align: right; width: 150px;"> Amount </th>
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            {{-- */$x=0;/* --}}
            @foreach($billinginvoice as $item)
                {{-- */$x++;/* --}}
                @if(empty($item->or_customer_id))
                <tr>
                    <td>{{ $x }}</td>
                    <td>{{ $item->first_name }} {{ $item->middle_name }} {{ $item->last_name }}{{ $item->company_name }}</td>
                    <td style="text-align: right;">{{ $item->date }}</td>
                    <td>{{ $item->bi_inv_no }}</td>
                    <td style="text-align: right;">{{ $item->bi_amount }}</td>
                    <td>
                        <a href="{{ url('/billing-invoice/' . $item->id) }}" class="btn btn-success btn-xs" title="View Billing Invoice"><i class="fa fa-eye"></i></a>
                        <a href="{{ url('/billing-invoice/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Billing Invoice"><i class="fa fa-pencil"></i></a>
                        {!! Form::open([
                            'method'=>'DELETE',
                            'url' => ['/billing-invoice', $item->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash"></i>', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete Billing Invoice',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ));!!}
                        {!! Form::close() !!}
                    </td>
                </tr>
                @endif
            @endforeach
            </tbody>
        </table>
        <div class="pagination"> {!! $billinginvoice->render() !!} </div>
    </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection