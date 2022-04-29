@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('header_style_preload')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Original Bottles Records</h1>
            </div>
        </div>
        <div style="clear: both; margin-bottom: 20px;"></div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-employee warning-msg-true">
            <p>{{ $message }}</p>
        </div>
    @endif

    <section class="content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover payroll-table" style="width:100%;">
                <tr>
					<th align="center">List No.</th>
					<th align="center">No. of bottles</th>
                    <th align="center">Date</th>
                    <th align="center">Action</th>
                </tr>
            {{--*/
                $original_bots_qty = 0;
            /*--}}
            @foreach ($original_bottles as $key => $value)
            {{--*/
                $original_bots_qty = $original_bots_qty + $value->orig_bottles;
            /*--}}
            <tr>
                <td>{{ ++$i }}</td>
                <td align="right">{{ $value->orig_bottles }}</td>
                <td align="center">{{ $value->date }}</td>
                <td align="center">
					<a href="{{ url('/wrm-original-bottles/' . $value->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit Bottles"><i class="fa fa-pencil"></i></a>
                </td>
            </tr>
            @endforeach
            </table>
            <h3>Total Original Bottles: <strong>{{ number_format($original_bots_qty) }}</strong></h3>
        </div>
        <div class="row" style="margin-right: 0px; margin-left: 0px;">
            <div class="pull-left" style="margin-bottom: 20px; margin-top: 20px;">
                <a href="{{ route('water-refilling-monitoring.index') }}" class="btn btn-primary">Back to Dashboard</a>
                <a href="{{ route('wrm-original-bottles.create') }}" class="btn btn-primary">Add orginal bottles</a>
            </div>
            <div class="pull-right">
             {!! $original_bottles->render() !!}
            </div>
        </div>
    </section>
</div>
@endsection

@section('footer_script_preload')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('footer_script')
<script type="text/javascript">
$('.datepicker').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
});

</script>
<style type="text/css">
    div.total-issued-bots ul{padding-left: 0;}
    div.total-issued-bots ul li{
        list-style: none;
        font-size: 22px;
    }
    li.total-bots-issued{border-top: 1px solid #000;}
</style>
@endsection