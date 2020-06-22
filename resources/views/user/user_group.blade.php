@php 
$active_menu='users'; 
$breadcrumbs = array(
    ['label'=>'Pengguna', 'url'=>route('users.profile')],
    ['label'=>'Grup Pengguna']
);
@endphp
@extends('layouts.app')
@section('content-header-right')
@if(has_action('user_groups', 'create'))
  <a href="{{route('user_groups.create')}}" class="btn btn-primary" ><i class="fas fa-plus"></i> Tambah Grup Pengguna</a>
@endif
@endsection
@section('title', 'Pengguna')
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('user._menu', ['active'=>'groups'])
    </div>
    <div class="col-md-9">
    @component('components.card')
      @include('dcru.dtables')
    @endcomponent
  </div>
</div>
@endsection
