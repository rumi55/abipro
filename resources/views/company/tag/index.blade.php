@php 
$active_menu='company'; 
$breadcrumbs = array(
    ['label'=>'Perusahaan', 'url'=>route('company.profile')],
    ['label'=>'Sortir']
);
@endphp
@section('content-header-right')
  <a href="{{route('tags.create')}}" class="btn btn-primary" ><i class="fas fa-plus"></i> Tambah Sortir</a>
@endsection
@extends('layouts.app')
@section('title', 'Sortir')
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('company._menu', ['active'=>'tags'])
    </div>
    <div class="col-md-9">
    @component('components.card')
      @include('dcru.dtables')
    @endcomponent
  </div>
</div>
@endsection