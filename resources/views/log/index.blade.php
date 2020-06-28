@php 
$active_menu='users'; 
$title = trans('User Activities');
$breadcrumbs = array(
    ['label'=>trans('Users'), 'url'=>route('users.profile')],
    ['label'=>$title]
);
@endphp
@extends('layouts.app')
@section('title', $title)
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('user._menu', ['active'=>'logs'])
    </div>
    <div class="col-md-9">
    @component('components.card')
      @include('dcru.dtables')
    @endcomponent
  </div>
</div>
@endsection
