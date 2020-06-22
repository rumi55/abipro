@php 
$active_menu='users'; 
$breadcrumbs = array(
    ['label'=>'Pengguna', 'url'=>route('users.profile')],
    ['label'=>'Daftar Pengguna']
);
@endphp
@extends('layouts.app')
@section('content-header-right')
  @if(has_action('users', 'create'))
  <a href="{{route('users.create')}}" class="btn btn-primary" ><i class="fas fa-plus"></i> Tambah Pengguna</a>
  @endif
@endsection
@section('title', 'Daftar Pengguna')
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('user._menu', ['active'=>'users'])
    </div>
    <div class="col-md-9">
    @component('components.card')
      @include('dcru.dtables')
    @endcomponent
  </div>
</div>
@endsection
