@php
$active_menu='settings';
$breadcrumbs = array(
    ['label'=>trans('Settings'), 'url'=>route('settings.index')],
    ['label'=>trans('Report Templates')]
);
@endphp
@extends('layouts.app')
@section('content-header-right')
@if(has_action('report_templates', 'create'))
  <a href="{{route('report_templates.create')}}" class="btn btn-primary" ><i class="fas fa-plus"></i> {{__('Add')}} {{__('Template')}}</a>
@endif
@endsection
@section('title', trans('Report Templates'))
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('setting._menu', ['active'=>'templates'])
    </div>
    <div class="col-md-9">
    @component('components.card')
      @include('dcru.dtables')
    @endcomponent
  </div>
</div>
@endsection
