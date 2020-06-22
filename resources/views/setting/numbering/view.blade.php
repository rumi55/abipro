@php 
$active_menu='$company'; 
$breadcrumbs = array(
    ['label'=>trans('Settings'), 'url'=>route('company.profile')],
    ['label'=>trans('Numbering'), 'url'=>route('numberings.index')],
    ['label'=>trans('Numbering Detail')]
);
@endphp
@extends('layouts.app')
@section('title', trans('Numbering Detail'))
@section('content')
<div class="row">
    <div class="col-md-3">
        @include('setting._menu', ['active'=>'numberings'])
    </div>
    <div class="col-md-9">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <a href="{{route('numberings.index')}}" data-toggle="tooltip" data-placement="top" title="Kembali ke Jenis Jurnal" ><i class="fa fa-chevron-left"></i></a> {{__('Detail Numbering')}}
          </h3>
          <div class="card-tools">
          @if(has_action('journal_type', 'edit'))
            <a href="{{route('numberings.edit', $model->id)}}" class="btn btn-tool" title="Edit"><i class="fa fa-edit"></i></a>
          @endif
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 col-sm-12">
                <strong>{{__('Numbering Name')}}</strong>
              <p class="text-muted">{{$model->name}}</p>
              <hr>
              <strong>{{__('Numbering Format')}}</strong>
              <p class="text-muted">{{$model->format}}</p>
              <hr>
              <strong>Counter Reset</strong>
              @php 
                $reset = array('y'=>trans('Yearly'), 'm'=>trans('Monthly'), 'd'=>trans('Daily'), 'n'=>trans('No Reset'));
              @endphp
              <p class="text-muted">{{$reset[$model->counter_reset]}}</p>
              <hr>
              <strong>Counter Digit</strong>
              <p class="text-muted">{{$model->counter_digit}}</p>
              <hr>
              <strong>Counter Start</strong>
              <p class="text-muted">{{$model->counter_start}}</p>
              <hr>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection