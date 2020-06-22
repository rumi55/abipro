<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{config('app.name')}}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 700;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
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
            }

            .title {
                font-size: 64px;
                color:#fff;
            }

            .links > a {
                color:#fff;
                padding: 25px 70px;
                font-size: 16px;
                font-weight: 700;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
                color: #fff;
                background-color: #007bff;
                border-color: #007bff;
                box-shadow: none;
                display: inline-block;
                text-align: center;
                vertical-align: middle;
                box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
                border-radius: .70rem;
            }
            a:hover{
                opacity:0.7;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
            .bg {
                background-image: url("img/welcome.jpg");/
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
            }
            .overlay {
                transition: .5s ease;
                opacity: 0.5;
                background-color:black;
                position: absolute;
                top: 0;
                left: 0;
                width:100%;
                height: 100%;
            }
        </style>
    </head>
    <body class="bg">
    <div class="overlay full-height"></div>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <img style="width:128px;height:128px" src="{{asset('img/apps75.png')}}" alt="{{ config('app.name', 'Webapp') }} Logo">
                <div class="title m-b-md">
                {{ config('app.name', 'Webapp') }}
                </div>
                <div class="links">
                    <a href="{{ url('/home') }}">Mulai</a>
                </div>
            </div>
        </div>
    </body>
</html>
