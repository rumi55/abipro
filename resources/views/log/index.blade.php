@php 
$active_menu='users'; 
$breadcrumbs = array(
    ['label'=>'Pengguna']
);
@endphp
@extends('layouts.app')
@section('title', 'Aktivitas Pengguna')
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
