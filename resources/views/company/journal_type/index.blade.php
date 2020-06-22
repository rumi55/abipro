@php 
$active_menu='company'; 
$breadcrumbs = array(
    ['label'=>'Perusahaan', 'url'=>route('company.profile')],
    ['label'=>'Jenis Jurnal']
);
@endphp
@extends('layouts.app')
@section('content-header-right')
@if(has_action('journal_type', 'create'))
  <a href="{{route('journal_types.create')}}" class="btn btn-primary" ><i class="fas fa-plus"></i> Tambah Jenis Jurnal</a>
@endif
@endsection
@section('title', 'Jenis Jurnal')
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('company._menu', ['active'=>'journal_types'])
    </div>
    <div class="col-md-9">
    @component('components.card')
      @include('dcru.dtables')
    @endcomponent
  </div>
</div>
@endsection
