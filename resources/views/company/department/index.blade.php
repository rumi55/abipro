@php 
$active_menu='company'; 
$breadcrumbs = array(
    ['label'=>'Perusahaan', 'url'=>route('company.profile')],
    ['label'=>'Departemen']
);
@endphp
@extends('layouts.app')
@section('content-header-right')
  <a href="{{route('departments.create')}}" class="btn btn-primary" ><i class="fas fa-plus"></i> Tambah Departemen</a>
@endsection
@section('title', 'Departemen')
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('company._menu', ['active'=>'departments'])
    </div>
    <div class="col-md-9">
    @component('components.card')
      @include('dcru.dtables')
    @endcomponent
  </div>
</div>
@endsection
