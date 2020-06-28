@php 
$active_menu='vouchers'; 
$breadcrumbs = array(
    ['label'=>trans('Vouchers')]
);
@endphp
@extends('layouts.app')
@section('content-header-right')
<div class="dropdown">
  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fas fa-plus"></i>{{__('Create Voucher')}}
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="{{route('vouchers.create.single', 'in')}}">{{__('Cash Receipt')}}</a>
    <a class="dropdown-item" href="{{route('vouchers.create.single', 'out')}}">{{__('Cash Payment')}}</a>
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