@php 
$active_menu='users'; 
$breadcrumbs = array(
    ['label'=>'Pengguna', 'url'=>route('users.profile')],
    ['label'=>'Aktivitas Pengguna']
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
    @slot('title') 
      <a href="{{route('logs.index')}}"><i class="fas fa-chevron-left"></i></a> Detail Aktivitas Pengguna 
      @endslot
      <strong>Pengguna</strong>
      <p class="text-muted"><a href="{{route('users.view', $log->created_by)}}">{{$log->user!=null?$log->user->name:'-'}}</a></p>
      <hr>
      <strong>Modul</strong>
      <p class="text-muted">{{$log->action!==null?$log->action->display_group:'-'}}</p>
      <hr>
      <strong>Aktivitas</strong>
      <p class="text-muted">{{$log->action!==null?$log->action->display_name:'-'}}</p>
      <hr>
      <strong>Referensi</strong>
      <p class="text-muted">-</p>
      <hr>
      <strong>Waktu</strong>
      <p class="text-muted">{{fdatetime($log->created_at)}}</p>
    @endcomponent
  </div>
</div>
@endsection
