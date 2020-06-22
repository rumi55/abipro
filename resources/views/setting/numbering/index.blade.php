@php 
$active_menu='settings'; 
$breadcrumbs = array(
    ['label'=>trans('Settings'), 'url'=>route('settings.index')],
    ['label'=>trans('Numberings')]
);
@endphp
@extends('layouts.app')
@section('content-header-right')
@if(has_action('numberings', 'create'))
  <a href="{{route('numberings.create')}}" class="btn btn-primary" ><i class="fas fa-plus"></i> {{__('Add')}} {{__('Numbering')}}</a>
@endif
@endsection
@section('title', trans('Numberings'))
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('setting._menu', ['active'=>'numberings'])
    </div>
    <div class="col-md-9">
    @component('components.card')
      @include('dcru.dtables')
    @endcomponent
  </div>
</div>
@endsection
