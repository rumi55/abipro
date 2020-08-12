@php
$active_menu='vouchers';
$breadcrumbs = array(
    ['label'=>trans('Vouchers')]
);
@endphp
@extends('layouts.app')
@section('content-header-right')
<div class="btn-group">
    <a href="{{route('vouchers.create')}}" class="btn btn-primary" ><i class="fas fa-plus"></i> {{__('Create Voucher')}}</a>
    <button type="button" class="btn btn-primary dropdown-toggle dropdown-icon" data-toggle="dropdown"></button>
    <span class="sr-only">Toggle Dropdown</span>
    <div class="dropdown-menu" role="menu">
        <a class="dropdown-item" href="{{route('vouchers.create.single', 'receipt')}}">{{__('Cash Receipt')}}</a>
        <a class="dropdown-item" href="{{route('vouchers.create.single', 'payment')}}">{{__('Cash Payment')}}</a>
    </div>
  </div>
@endsection
@section('title', trans('Vouchers'))
@section('content')
<div class="row">
    <div class="col-md-12">
    @component('components.card')
      @include('dcru.dtables')
    @endcomponent
  </div>
</div>
@endsection
