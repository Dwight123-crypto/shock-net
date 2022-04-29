<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Shock Net</title>


    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	@yield('header_metas')

	@yield('header_style_preload')

    <link href="{{ asset('/assets/css/main.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
	@yield('header_style_postload')
    <style>
        li.treeview.active{overflow-y: scroll; max-height: 220px;}
    </style>
</head>
<body class="skin-blue @yield('body_classes')">

    <div class="wrapper">
        @include('_includes.header')
        @include('_includes.sidebar')

        @yield('content')

        @include('_includes.footer')
    </div>

</body>
<!-- JavaScripts -->
<script src="{{asset('/assets/js/vendor.js')}}"></script>
@yield('footer_script_preload')
<script src="{{asset('/assets/js/app.js')}}"></script>
<script src="{{asset('/assets/js/accounting.js')}}"></script>
<script src="/assets/js/JsBarcode.all.min.js"></script>
@yield('footer_script')
</html>
