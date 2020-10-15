<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Webapp') }}  - @yield('title')</title>
  <link rel="icon" type="image/png" sizes="96x96" href="{{asset('img/logo-color.png')}}">
@include('layouts.css')

</head>
<body class="hold-transition sidebar-mini pace-primary  layout-navbar-fixed"  onresize="resize()" style="overflow: hidden;">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark navbar-purple elevation-2 border-bottom-0">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown notifications">
            <a class="nav-link" data-toggle="dropdown" href="#">
              <i class="far fa-bell"></i>
              <span class="badge badge-warning navbar-badge notification-count"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
              <span class="dropdown-item dropdown-header header"></span>
              <div class="dropdown-divider"></div>
              <span id="list-notifications">
              <a href="#" class="dropdown-item">
              8 friend requests
                <span class="float-right text-muted text-sm">12 hours</span>
              </a>
              <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item">
                <i class="fas fa-file mr-2"></i> 3 new reports
                <span class="float-right text-muted text-sm">2 days</span>
              </a>
              <div class="dropdown-divider"></div>
              </span>
            </div>
          </li>
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
          <img src="{{asset(empty(user('photo'))?'img/user2-160x160.jpg':url_file(user('photo')))}}" class="user-image img-circle elevation-2" alt="User Image"  >
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <!-- User image -->

          <li class="user-header bg-gray">
          <img src="{{asset(empty(user('photo'))?'img/user2-160x160.jpg':url_file(user('photo')))}}" class="img-circle elevation-2" alt="User Image">
            <p>
              {{ user('name')}}<br/>
              {{ user('group.display_name')}}
            </p>
          </li>
          <!-- Menu Footer-->
          <li class="user-footer">
            <a title="{{__('User Profile')}}" href="{{route('users.profile')}}" class="btn btn-default btn-flat"><i class="fas fa-user"></i> {{__('Profile')}}</a>
            <a title="{{__('Logout')}}" class="btn btn-default btn-flat bg-red float-right" href="{{ route('logout') }}"
               onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
            </a>
            <a href="{{route('user.password')}}" title="{{__('Change Password')}}" class="btn btn-default bg-warning btn-flat float-right"><i class="fas fa-key"></i></a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

@include('layouts.sidebar')


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header elevation-1 mb-1">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-12">
            @include('layouts.breadcrumbs')
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-sm-6">
            <h3 class="m-0 text-dark">@yield('title')</h3>
          </div><!-- /.col -->
          <div class="col-sm-6 text-right">
            @yield('content-header-right')
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
<iframe id="viewer" src="{{asset('/pdf/web/viewer.html')}}@if(isset($file))?file={{$file}} @endif" width="100%"  frameborder="0"></iframe>
    @yield('content')
  </div>
  @include('layouts.footer')
</div>
@include('layouts.js')
@stack('js')
@include('layouts.sweetalert')
<script>
    resize()
    function resize(){
        var frame = document.getElementById('viewer');
    var h = window.innerHeight-227;
    frame.height = h;
    }
</script>
</body>
</html>
