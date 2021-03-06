<?php 
$months = [
    '01' => 'January',
    '02' => 'February',
    '03' => 'March',
    '04' => 'April',
    '05' => 'May',
    '06' => 'June',
    '07' => 'July',
    '08' => 'August',
    '09' => 'September',
    '10' => 'October',
    '11' => 'November',
    '12' => 'December'
];
?>
        {!! Form::open(array('url' => 'report/sales-report-dat','method'=>'get')) !!}
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Sales</h3>
            </div>

            <div class="box-body">
            <div class="row">

              <div class="col-sm-6">
                  <div class="form-group">
                    {!! Form::label('month', 'Month', ['class' => 'control-label']) !!}
                    {!! Form::select('month', $months, '01', ['class' => 'form-control']) !!}
                  </div>
              </div>
              <div class="col-sm-6">
                  <div class="form-group">
                    {!! Form::label('year', 'Year', ['class' => 'control-label']) !!}
                    <?
                      $current_year = date('Y');
                      $earliest_year = 2010;
                    ?>
                    <select name="year" class="form-control" autocomplete="off" required>'
                      @foreach (range(date('Y'), $earliest_year) as $x)
                          <option value="{{$x}}">{{$x}}</option>
                      @endforeach
                    </select>
                  </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group">
                  {!! Form::label('report_type_label', 'Type of Report', ['class' => 'control-label']) !!}
                  <select name="report_type" class="form-control" autocomplete="off" required>'
                    <option value="">Please Select</option>
                    <option value="fiscal">Fiscal</option>
                    <option value="calendar">Calendar</option>
                  </select>
                </div>
              </div>
            </div>
            </div>
            <!-- /.box-body -->

            <div class="box-footer">
                    <div class="btn-group">
                      <button type="submit" name="action" value="dat" class="btn btn-default btn-dat"><i class="fa fa-file text-info"></i> DAT</button>
                      <button type="submit" name="action" value="csv" class="btn btn-default btn-csv"><i class="fa fa-file-excel-o text-success"></i> CSV</button>
                    </div>
            </div>
          </div>
          <!-- /.box -->
        {!! Form::close() !!}
