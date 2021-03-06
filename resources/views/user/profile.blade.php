@php 
$active_menu='users'; 
$breadcrumbs = array(
    ['label'=>trans('Users')]
);
@endphp
@extends('layouts.app')
@section('title', trans('Users'))
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('user._menu', ['active'=>'profile'])
    </div>
    <div class="col-md-9">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">{{__('User Profile')}}</h3>
          <div class="card-tools">
            <a href="{{route('users.profile.edit')}}" class="btn btn-tool" title="{{__('Edit Profile')}}"><i class="fa fa-user-edit"></i></a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3 col-sm-12">
              <div class="text-center">
                <img class="profile-user-img img-fluid img-circle" src="{{url_image($user->photo)}}" alt="User profile picture">
              </div>
              <h3 class="profile-username text-center">{{$user->name}}</h3>
            </div>
            <div class="col-md-9 col-sm-12">
              <strong><i class="far fa-envelope mr-1"></i> {{__('Email')}}</strong>
              <p class="text-muted">{{$user->email}}</p>
              <hr>
              <strong><i class="fas fa-phone mr-1"></i> {{__('Phone')}}</strong>
              <p class="text-muted">{{$user->phone}}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection

