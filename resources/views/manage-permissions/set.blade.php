@extends('layouts.adminlte')

@section('body_classes')

@if(isset($view_name)){{$view_name}}@endif

@endsection

@section('content')

<div class="content-wrapper">
    <div class="row">
        <section class="content-header">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h1>Set permissions for role - {{$role->role_name}}</h1>
            </div>
            <div class="pull-right">
                <a href="/manage-roles" class="btn btn-primary"><i class="fa" aria-hidden="true"></i> Back</a>
            </div>
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
<?php
//     // Get a collection of all the routes
// $routeCollection = Route::getRoutes();

// // Create your base array of routes
// $routes = [];

// // loop through the collection of routes
// foreach ($routeCollection as $route) {

//     // get the action which is an array of items
//     $action = $route->getAction();

//     // if the action has the key 'controller' 
//     if (array_key_exists('controller', $action)) {

//         // explode the string with @ creating an array with a count of 2
//         $explodedAction = explode('@', $action['controller']);

//         // check to see if an array exists for the controller name
//         if (!isset($routes[$explodedAction[0]])) {

//             // if not create it, this will look like
//             // $routes['controllerName']
//             $routes[$explodedAction[0]] = [];
//         }
//         // set the add the method name to the controller array
//         $routes[$explodedAction[0]][] = $explodedAction[1];
//     }
// }

// // show the glory of your work
// dd($routes);
?>

    <section class="content">
        {!! Form::open(array('route' => 'manage-permissions.store','method'=>'POST')) !!}
        <div class="table-responsive">
            <input type="hidden" name="roles_id" value="{{$role->id}}"/>
            <table class="table table-bordered table-striped table-hover table-fixed">
                <thead>
                    <tr class="com-functions">
                        <th colspan="2"class="comp">COMPONENTS</th>
                        <th colspan="10" class="func">FUNCTIONS</th>
                    </tr>
                    <tr class="com-functions">
                        <th></th>
                        <th>Select All</th>
                        <th>Index</th>
                        <th>Add</th>
                        <th>Edit</th>
                        <th>Show</th>
                        <th>Delete</th>
                        <th>Details</th>
                        <th>Report</th>
                        <th>Reset</th>
                        <th>Export</th>
                        <th>Generate QR code</th>
                    </tr>
                </thead>
                @foreach($controllers as $key=>$controller_data)
                <tr class="row-checkbox">
                    <td>{{$controller_data['controller_name']}}</td>
                    <td align="center"><input type="checkbox" class="select_all" name="select_all"></td>
                    <td align="center">
                        @if(array_key_exists('index', $controller_data['methods']))
                        <input type="checkbox" class="checkbox" name="permission[{{$key}}.index]" value="1" @if($controller_data['methods']['index'] == '1') checked @endif>
                        <input type="hidden" name="permission[{{$key}}.index]" value="0" @if($controller_data['methods']['index'] == '1') disabled @endif>
                        @endif
                    </td>
                    <td align="center">
                        @if(array_key_exists('add', $controller_data['methods']))
                        <input type="checkbox" class="checkbox" name="permission[{{$key}}.add]" value="1" @if($controller_data['methods']['add'] == '1') checked @endif>
                        <input type="hidden" name="permission[{{$key}}.add]" value="0" @if($controller_data['methods']['add'] == '1') disabled @endif>
                        @endif
                    </td>
                    <td align="center">
                        @if(array_key_exists('edit', $controller_data['methods']))
                        <input type="checkbox" class="checkbox" name="permission[{{$key}}.edit]" value="1" @if($controller_data['methods']['edit'] == '1') checked @endif>
                        <input type="hidden" name="permission[{{$key}}.edit]" value="0" @if($controller_data['methods']['edit'] == '1') disabled @endif>
                        @endif
                    </td>
                    <td align="center">
                        @if(array_key_exists('show', $controller_data['methods']))
                        <input type="checkbox" class="checkbox" name="permission[{{$key}}.show]" value="1" @if($controller_data['methods']['show'] == '1') checked @endif>
                        <input type="hidden" name="permission[{{$key}}.show]" value="0" @if($controller_data['methods']['show'] == '1') disabled @endif>
                        @endif
                    </td>
                    <td align="center">
                        @if(array_key_exists('delete', $controller_data['methods']))
                        <input type="checkbox" class="checkbox" name="permission[{{$key}}.delete]" value="1" @if($controller_data['methods']['delete'] == '1') checked @endif>
                        <input type="hidden" name="permission[{{$key}}.delete]" value="0" @if($controller_data['methods']['delete'] == '1') disabled @endif>
                        @endif
                    </td>
                    <td align="center">
                        @if(array_key_exists('view_details', $controller_data['methods']))
                        <input type="checkbox" class="checkbox" name="permission[{{$key}}.view_details]" value="1" @if($controller_data['methods']['view_details'] == '1') checked @endif>
                        <input type="hidden" name="permission[{{$key}}.view_details]" value="0" @if($controller_data['methods']['view_details'] == '1') disabled @endif>
                        @endif
                    </td>
                    <td align="center">
                        @if(array_key_exists('report', $controller_data['methods']))
                        <input type="checkbox" class="checkbox" name="permission[{{$key}}.report]" value="1" @if($controller_data['methods']['report'] == '1') checked @endif>
                        <input type="hidden" name="permission[{{$key}}.report]" value="0" @if($controller_data['methods']['report'] == '1') disabled @endif>
                        @endif
                    </td>
                    <td align="center">
                        @if(array_key_exists('reset', $controller_data['methods']))
                        <input type="checkbox" class="checkbox" name="permission[{{$key}}.reset]" value="1" @if($controller_data['methods']['reset'] == '1') checked @endif>
                        <input type="hidden" name="permission[{{$key}}.reset]" value="0" @if($controller_data['methods']['reset'] == '1') disabled @endif>
                        @endif
                    </td>
                    <td align="center">
                        @if(array_key_exists('export', $controller_data['methods']))
                        <input type="checkbox" class="checkbox" name="permission[{{$key}}.export]" value="1" @if($controller_data['methods']['export'] == '1') checked @endif>
                        <input type="hidden" name="permission[{{$key}}.export]" value="0" @if($controller_data['methods']['export'] == '1') disabled @endif>
                        @endif
                    </td>
                    <td align="center">
                        @if(array_key_exists('qr_code', $controller_data['methods']))
                        <input type="checkbox" class="checkbox" name="permission[{{$key}}.qr_code]" value="1" @if($controller_data['methods']['qr_code'] == '1') checked @endif>
                        <input type="hidden" name="permission[{{$key}}.qr_code]" value="0" @if($controller_data['methods']['qr_code'] == '1') disabled @endif>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        <div class="row" style="margin-right: 0px; margin-left: 0px;">
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
        {!! Form::close() !!}
    </section>
</div>
@endsection

@section('footer_script')
<script>
    $( document ).ready(function() {

        $(".checkbox").change(function() {
            if($(this).is(":checked")){
                $(this).next().prop('disabled', true);
            }else{
                $(this).next().prop('disabled', false);
            }
        });

        $('.select_all').click(function(e){
            var row_check = $(e.target).closest('tr.row-checkbox');
            $('input:checkbox', row_check).prop('checked',this.checked);
            $('input[type=hidden]', row_check).prop('disabled',this.checked);
        });
    });
</script>

<style type="text/css">
    ul.pagination{
        margin: 0px;
    }
    .table-responsive table{
        font-size: 14px;
        table-layout: fixed;
    }
    /* .actions{
        width: 300px!important;
    } */
    .alert-success{
        margin: 15px;
    }
    .com-functions th{
        text-align: center;
    }
    .comp{
        background: #3C8DBC;
        color: #fff;
    }
    .func{
        background: #1FA67A;
        color: #fff;
    }
    .table-responsive{
        margin-bottom: 10px;
    }
    tbody {
        display:block;
        height:500px;
        overflow:auto;
    }
    thead, tbody tr {
        display:table;
        width:100%;
        table-layout:fixed;
    }

    @media screen and (max-width: 767px){
        tbody {
            display:contents!important;
            height:unset!important;
            overflow:unset!important;
        }
        thead, tbody tr {
            display:table-header-group!important;
            width:unset!important;
            table-layout:unset!important;
        }
        .table-responsive table{
            table-layout: unset!important;
        } 
    }
</style>
@endsection