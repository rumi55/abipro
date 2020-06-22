@php 
$active_menu='$company'; 
$breadcrumbs = array(
    ['label'=>'Perusahaan', 'url'=>route('company.profile')],
    ['label'=>'Jenis Jurnal', 'url'=>route('journal_types.index')],
    ['label'=>'Detail Jenis Jurnal']
);
@endphp
@extends('layouts.app')
@section('title', 'Detail Jenis Jurnal')
@section('content')
<div class="row">
    <div class="col-md-3">
        @include('company._menu', ['active'=>'journal_types'])
    </div>
    <div class="col-md-9">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <a href="{{route('journal_types.index')}}" data-toggle="tooltip" data-placement="top" title="Kembali ke Jenis Jurnal" ><i class="fa fa-chevron-left"></i></a> Jenis Jurnal
          </h3>
          <div class="card-tools">
          @if(has_action('journal_type', 'edit'))
            <a href="{{route('journal_types.edit', $model->id)}}" class="btn btn-tool" title="Edit Jenis Jurnal"><i class="fa fa-edit"></i></a>
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
              <p class="text-muted">{{$model->counter_reset}}</p>
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