@php 
$active_menu='users'; 
$title = 'User Group';
$breadcrumbs = array(
    ['label'=>trans('Users'), 'url'=>route('users.profile')],
    ['label'=>$title]
);
@endphp
@extends('layouts.app')
@section('content-header-right')
@if(has_action('user_groups', 'create'))
  <a href="{{route('user_groups.create')}}" class="btn btn-primary" ><i class="fas fa-user-plus"></i> {{__('Add').' '.__('User Group')}}</a>
@endif
@endsection
@section('title', $title)
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
