<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ config('app.name', 'Abipro') }} | @yield('title')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{asset('css/adminlte.min.css')}}">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  @include('layouts.css')
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
      <a href="{{route('home')}}" class="navbar-brand text-center">
        <img src="{{asset('img/logo-color.png')}}" alt="Abipro Logo" class="brand-image img-circle elevation-2"
             style="opacity: .8">
        <span class="brand-text font-weight-light">Abipro</span>
      </a>
    </div>
  </nav>
  <div class="content-wrapper">
    <div class="content-header p-5" style="background:transparent">
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h1 class="m-0 text-dark text-center">@yield('title')</h1>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
        <div class="container">
          @yield('content')
        </div>
    </div>
  </div>
@include('layouts.footer')
</div>
@include('layouts.js')
@stack('js')
@include('layouts.sweetalert')
</body>
</html>
