@php 
$active_menu='settings'; 
$breadcrumbs = array(
    ['label'=>'Settings']
);
@endphp
@extends('layouts.app')
@section('title', trans('Settings'))
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-3">
      @include('setting._menu', ['active'=>'general'])
    </div>
    <div class="col-md-9">
      <div class="card">
        <div class="card-body">
          
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
