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
                    <h1>Payroll <a href="{{ route('list-employees.index') }}" class="btn btn-primary btn-xs" title="Add Employee to Payroll"><i class="fa fa-plus" aria-hidden="true"></i></a></h1>
                </div>
                
        </div>
        <div style="clear: both;"></div>
        </section>
    </div>

    <section class="content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover payroll-table" style="width:100%;">
                <tr>
                    <th rowspan="2" align="center" class="ename">Name</th>
                    <th rowspan="2" align="center" class="tin-no">TIN</th>
                    <th rowspan="2" align="center">Status</th>
                    <th colspan="3" align="center">Salary</th>
                    <th colspan="3" align="center">Overtime</th>
                    <th rowspan="2" align="center">COLA</th>
                    <th rowspan="2" align="center">Total</th>
                    <th colspan="3" align="center">Employee's Contribution</th>
                    <th colspan="3" align="center">Late</th>
                    <th colspan="3" align="center">Employer's Contribution</th>
                    <th rowspan="2" align="center">Net Pay</th>
                    <th rowspan="2" align="center">Tax Withheld</th>
                </tr>
                <tr>
                    <th>Days Present</th>
                    <th>Rate</th>
                    <th>Total</th>
                    <th>Overtime</th>
                    <th>Rate</th>
                    <th>Total</th>
                    <th>SSS</th>
                    <th>PHIC</th>
                    <th>PAG IBIG</th>
                    <th>Late</th>
                    <th>Rate</th>
                    <th>Total</th>
                    <th>SSS</th>
                    <th>PHIC</th>
                    <th>PAG IBIG</th>
                </tr>
                
                <tr>
                    <td>{{ $employee->employee_name }}</td>
                    <td>{{ $employee->tin_no }}</td>
                    <td>{{ $employee->status }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </section>
</div>
@endsection