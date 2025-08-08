@extends('layouts.adminlte')
@section('body_classes')
@if(isset($view_name)){{$view_name}}@endif
@endsection

@section('header_style_postload')
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<style>
.pink {
  border-top-color: #FF6384;
}
.green {
  border-top-color: #00a65a;
}
.yellow {
  border-top-color: #f39c12;
}
</style>
@endsection
@section('content')
<?php

  $sales = App\ChartAccount::sales();
  
  $over_all_revenue = array_map(function ($x, $y) { 
    return $x-$y; 
  } , $sales->sales, $sales->expenses);

  $month = date('M');
  $firstDayofYear = new Carbon\Carbon('first day of January');
  $thisDay = Carbon\Carbon::now();
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <br/>
    {{-- <div class="form-group">
        <form class="form-horizontal">
            <div class="row">
               <div class="col-md-4"></div>
                    <div class="col-md-4" style="padding-right:0px;">
                        <input name="daterange" type="text" value="" class="form-control" readonly> 
                    </div>
                    <div class="col-md-4" style="padding-left:0px;">
                        <button type="button" id="search" class="btn btn-box-tool">
                        <i class="fa fa-search fa-lg"></i>
                </div>
            </div>
        </form>
    </div> --}}
    <!-- style="padding-right:0px;" -->
    <section class="content-header">
        <h1>
            Charts
            <small></small>
        </h1>

    </section>

    <!-- Main content -->
    <section class="content">

      <div class="row">
      
        <div class="col-xs-12 col-md-6 col-lg-4">
          <!-- LINE CHART -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Total Sales</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="chart-sales" style="height:250px"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        
        <div class="col-xs-12 col-md-offset-3 col-md-6 col-lg-offset-0 col-lg-4">
          <!-- LINE CHART -->
          <div class="box pink">
            <div class="box-header with-border">
              <h3 class="box-title">Total Expenses</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="chart-expenses" style="height:250px"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->

        <div class="col-xs-12 col-md-6 col-lg-4">
          <!-- LINE CHART -->
          <div class="box green">
            <div class="box-header with-border">
              <h3 class="box-title">Total Revenues</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="chart-cost" style="height:250px"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        
      </div>
      <!-- /.row -->

      <div class="row">
      
        <div class="col-xs-12">
          <!-- LINE CHART -->
          <div class="box yellow">
            <div class="box-header with-border">
              <h3 class="box-title">Total Taxes (monthly or accumulated)</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="chart-taxes" style="height:250px"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      
      </div>
      <!-- /.row -->

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection


@section('footer_script_preload')
<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>
<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
@endsection


@section('footer_script')
<script>
(function() {
    var month = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var chartbgcolor = { blue:"rgb(0, 192, 239, 0.5)",pink:"rgb(255, 99, 132, 0.5)",green:"rgb(0, 166, 90, 0.5)",yellow:"rgb(243, 156, 18, 0.5)" };
    var chartbcolor = { blue:"rgb(0, 192, 239)",pink:"rgb(255, 99, 132)",green:"rgb(0, 166, 90)",yellow:"rgb(243, 156, 18)" };
    var chartLabelMonths = labelMonth('<?php echo $firstDayofYear; ?>', '<?php echo $thisDay; ?>');
    
    var chart_name = ["chart-sales","chart-cost","chart-expenses","chart-taxes"];
    // var chartSales = new Chart(chart_name[0], charts(<?php // echo json_encode($pos_sales->sales); ?>, 'Sales', chartbgcolor.blue, chartbcolor.blue));
    // var chartCosts = new Chart(chart_name[1], charts(<?php // echo json_encode($pos_sales->costs); ?>, 'Costs', chartbgcolor.pink, chartbcolor.pink));
    // var chartExpenses = new Chart(chart_name[2], charts(<?php // echo json_encode($over_all_expenses); ?>, 'Expenses', chartbgcolor.green, chartbcolor.green));
    // var chartTaxes = new Chart(chart_name[3], charts(<?php // echo json_encode($pos_sales->taxes); ?>, 'Taxes', chartbgcolor.yellow, chartbcolor.yellow));
    var chartSales = new Chart(chart_name[0], charts(<?php echo json_encode($sales->sales); ?>, 'Sales', chartbgcolor.blue, chartbcolor.blue));
    var chartExpenses = new Chart(chart_name[2], charts(<?php echo json_encode($sales->expenses); ?>, 'Expenses', chartbgcolor.pink, chartbcolor.pink));
    // var chartCosts = new Chart(chart_name[1], charts(<?php //echo json_encode($sales->costs); ?>, 'Revenues', chartbgcolor.pink, chartbcolor.pink));
    var chartCosts = new Chart(chart_name[1], charts(<?php echo json_encode($over_all_revenue); ?>, 'Revenues', chartbgcolor.green, chartbcolor.green));
    var chartTaxes = new Chart(chart_name[3], charts(<?php echo json_encode($sales->taxes); ?>, 'Taxes', chartbgcolor.yellow, chartbcolor.yellow));
    function charts(data, label, bgcolor, bcolor) {
      return {
              type: 'line',
              data: {
                    labels: chartLabelMonths,
                  datasets: [{
                      backgroundColor: bgcolor,
                      borderColor: bcolor,
                      data: data,
                      label: label,
                      fill: 'start'  //false, origin, start, end
                  }]
              },
              options: {
                  scales: {
                      xAxes: [{
                          gridLines: { display: false }
                      }],
                      yAxes: [{
                          gridLines: { display: false }
                      }]
                  }
              }
          };
    }

    $('#search').on('click', function() {
      var url = '{{ url("voucher/get-date-range") }}'
      var daterange = $('input[name=daterange]').val();
      var mon = daterange.split('-');
      if(daterange!='') {
           //console.log(label_months);
        var dataReq = { daterange: daterange }
        $.get(url, dataReq, function(data) {
          console.log(data);
          chartSales.data.labels = labelMonth(mon[0], mon[1]);
          chartCosts.data.labels = labelMonth(mon[0], mon[1]);
          chartExpenses.data.labels = labelMonth(mon[0], mon[1]);
          chartTaxes.data.labels = labelMonth(mon[0], mon[1]);

          // chartSales.data.datasets[0].data = data[0].sales;
          // chartCosts.data.datasets[0].data = data[0].costs;
          // chartExpenses.data.datasets[0].data = data[0].expenses;
          // chartSales.update()
          // chartCosts.update()
          // chartExpenses.update()
          // chartTaxes.update()
          //console.log(data[0].sales);
        });
      } else {
        alert('Select Date');
      }  
    });

    function labelMonth(mfrom, mto) {
      mfrom = new Date(mfrom),
      mto = new Date(mto),
      //console.log(mfrom+'-'+mto);
      label_months = [];

      for(var i=mfrom.getMonth();i<=mto.getMonth();i++) {
        label_months.push(month[i]);
      }
      return label_months;
    }

    $('input[name="daterange"]').daterangepicker({
        autoUpdateInput: false,
        "opens":"left",
        locale: {
          cancelLabel: 'Close'
      }
    });

    $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
    });
    
    //'chart-sales' 'chart-cost' 'chart-expenses' 'chart-taxes'
    
}());
</script>
@endsection