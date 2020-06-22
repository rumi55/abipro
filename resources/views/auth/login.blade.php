@extends('auth.layout')
@section('title', 'Login')  
@section('content')  
  <form method="POST" action="{{ route('login') }}">
      @csrf
        <div class="input-group mb-3">
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
          @error('email')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>
        <div class="input-group mb-3">
          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
          @error('password')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
              {{ __('Remember Me') }}
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">
            {{ __('Login') }}
            </button>
          </div>
          <!-- /.col -->
        </div>
          <div class="row mt-5 mb-3">
            <div class="col-sm-6">
              <a href="#">Lupa Password?</a>
            </div>
            <div class="col-sm-6 text-right">
              <a href="{{route('register')}}" >Daftar Sekarang</a>
            </div>
          </div>
      </form>
      <div class="mt-5 text-center">
      <small>Copyright &copy; {{date('Y')=='2020'?date('Y'):'2020-'.date('Y')}} <a href="{{url('/')}}">{{ config('app.name', 'Webapp') }}</a>. All rights reserved.</small>
      </div>
@endsection