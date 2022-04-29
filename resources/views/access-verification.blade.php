<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Access Verification | Shock Net</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                /* height: 100vh; */
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
                max-width: 520px;
                width: 100%;
                margin: 25px;
            }

            .title {
                font-size: 50px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 0px;
                font-family: sans-serif;
            }
            .time{
                margin-bottom: 30px;
            }
            .form-timein{
                text-align: left;
            }
            .form-timein input, select{
                display: block;
                width: 95%;
                height: 34px;
                padding: 5px 12px;
                font-size: 14px;
                line-height: 1.42857143;
                color: #555;
                background-color: #fff;
                background-image: none;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                -webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
                transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
            }
            select{
                width: 100%;
                height: 45px;
                max-width: 250px;
            }
            .form-timein .btn{
                box-shadow: none;
                color: #fff;
                display: inline-block;
                padding: 12px 12px;
                margin-bottom: 0;
                font-size: 14px;
                font-weight: 600;
                line-height: 1.42857143;
                text-align: center;
                white-space: nowrap;
                vertical-align: middle;
                -ms-touch-action: manipulation;
                touch-action: manipulation;
                cursor: pointer;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                background-image: none;
                border: 1px solid transparent;
                border-radius: 4px;
                background-color: #3c8dbc;
                border-color: #367fa9;
                width: 100%;
                max-width: 200px;
                text-decoration: unset;
                margin-bottom: 10px;
            }
            .form-timein label{
                font-weight: 600;
            }
            .form-timein .row{
                margin-bottom: 10px;
            }
            div.alert-success p.notification{
                color: #0B8010;
                font-weight: 600;
            }
            div.alert-warning p.notification{
                color: #FD0017;
                font-weight: 600;
            }
            p.notification{
                font-family: sans-serif;
            }
            p.note{
                font-family: sans-serif; 
                text-align: center; 
                line-height: 25px;
            }
            img{width: 100%;}
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">

            <div class="content">
                <div class="title m-b-md" style="font-family: unset; font-size: 60px;">
                    <h5>Access verification for creating QR code</h5>
                </div>
                <div class="row">
                    <div class="col-md-12 form-timein">
                        {!! Form::open(array('url' => 'generate-employee-qrcode','method'=>'post')) !!}
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Username:</label>
                                <input type="text" name="username" class="form-control" placeholder="Enter username" required/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Password:</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter password" required/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary">Generate QR code</button>
                                <a href="{{ url('time-in-time-out') }}" class="btn btn-primary"><<< Back</a>
                            </div>
                        </div>
                        {!! Form::close() !!}

                        @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible">
                            <p class="notification">{{ $message }}</p>
                        </div>
                        @endif

                        @if ($message = Session::get('warning'))
                        <div class="alert alert-warning alert-dismissible">
                            <p class="notification">{!! nl2br($message) !!}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </body>
    @include('setcookies');
</html>