@php
$active_menu='$company';
$breadcrumbs = array(
    ['label'=>trans('Settings'), 'url'=>route('company.profile')],
    ['label'=>trans('Report Templates'), 'url'=>route('report_templates.index')],
    ['label'=>trans('Report Template Detail')]
);
@endphp
@extends('layouts.app')
@section('title', trans('Report Template Detail'))
@section('content')
<div class="row">
    <div class="col-md-3">
        @include('setting._menu', ['active'=>'templates'])
    </div>
    <div class="col-md-9">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <a href="{{route('report_templates.index')}}" data-toggle="tooltip" data-placement="top" ><i class="fa fa-chevron-left"></i></a> {{__('Report Template Detail')}}
          </h3>
          <div class="card-tools">
          @if(has_action('report_templates', 'edit'))
            <a href="{{route('report_templates.edit', $model->id)}}" class="btn btn-tool" title="Edit"><i class="fa fa-edit"></i></a>
          @endif
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 col-sm-12">
                <strong>{{__('Template Name')}}</strong>
              <p class="text-muted">{{$model->template_name}}</p>
              <strong>{{__('Type')}}</strong>
              <p class="text-muted">{{$model->report_name}}</p>
              <strong>Template Content</strong>
              <p class="text-muted">{!! $model->template_content !!}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection
