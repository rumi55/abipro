@php 
$active_menu='$company'; 
$mode_title = $mode=='create'?'Tambah':'Edit';
$breadcrumbs = array(
    ['label'=>'Perusahaan', 'url'=>route('company.profile')],
    ['label'=>'Jenis Jurnal', 'url'=>route('journal_types.index')],
    ['label'=>$mode_title.' Jenis Jurnal']
);
@endphp
@extends('layouts.app')
@section('title', 'Jenis Jurnal')
@section('content')
<div class="row">
    <div class="col-md-3">
        @include('company._menu', ['active'=>'journal_types'])
    </div>
    <div class="col-md-9">
    @component('components.card_form', [
        'id'=>'user-form','action'=>$mode=='create'?route('journal_types.create.save'):route('journal_types.edit.update', $model->id),
        'method'=>$mode=='create'?'POST':'PUT',
        'btn_label'=>$mode=='create'?'Buat Baru':'Simpan',

    ])
      @slot('title') 
      <a href="{{route('journal_types.index')}}"><i class="fas fa-chevron-left"></i></a> {{$mode_title}} Jenis Jurnal 
      @endslot
        <div class="form-group row">
            <label for="name" class="col-sm-3 col-form-label">Nama Jurnal</label>
            <div class="col-md-9 col-sm-9">
            <input type="text" required class="form-control @error('name') is-invalid @enderror  @error('display_name') is-invalid @enderror" name="name" id="name" value="{{old('name', $model->name)}}">
            @error('name') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="format" class="col-sm-3 col-form-label">Format Penomoran</label>
            <div class="col-md-9 col-sm-9">
                <input type="text" required class="form-control @error('format') is-invalid @enderror  @error('format') is-invalid @enderror" name="format" id="format" value="{{old('format', $model->format)}}">
                <small class="text-muted"></small>
                @error('format') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="counter_reset" class="col-sm-3 col-form-label">Conter Reset</label>
            <div class="col-md-3 col-sm-3">
                <select class="select2 form-control" name="counter_reset">
                    <option {{old('counter_reset', $model->counter_reset)=='y'?'selected':''}} value="y">Tahunan</option>
                    <option {{old('counter_reset', $model->counter_reset)=='m'?'selected':''}} value="m">Bulanan</option>
                    <option {{old('counter_reset', $model->counter_reset)=='d'?'selected':''}} value="d">Harian</option>
                    <option {{old('counter_reset', $model->counter_reset)=='n'?'selected':''}} value="n">Tidak Direset</option>
                </select>
                @error('counter_reset') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="counter_digit" class="col-sm-3 col-form-label">Conter Digit</label>
            <div class="col-md-2 col-sm-2">
                <input type="number" name="counter_digit" class="form-control"  value="{{old('counter_digit', $model->counter_digit)}}" />
                @error('counter_digit') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
            <label for="counter_start" class="col-sm-2 col-form-label">Conter Start</label>
            <div class="col-md-2 col-sm-2">
                <input type="number" name="counter_start" class="form-control" value="{{old('counter_start', $model->counter_start)}}" />
                @error('counter_start') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
    @endcomponent
  </div>
</div>
@endsection