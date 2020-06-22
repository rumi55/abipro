@php 
$active_menu='settings'; 
$mode_title = $mode=='create'?trans('Add'):trans('Edit');
$breadcrumbs = array(
    ['label'=>trans('Settings'), 'url'=>route('settings.index')],
    ['label'=>trans('Numbering'), 'url'=>route('numberings.index')],
    ['label'=>$mode_title.' '.trans('Numbering')]
);
@endphp
@extends('layouts.app')
@section('title', trans('Numbering'))
@section('content')
<div class="row">
    <div class="col-md-3">
        @include('setting._menu', ['active'=>'numberings'])
    </div>
    <div class="col-md-9">
    @component('components.card_form', [
        'id'=>'user-form','action'=>$mode=='create'?route('numberings.create.save'):route('numberings.edit.update', $model->id),
        'method'=>$mode=='create'?'POST':'PUT',
        'btn_label'=>$mode=='create'?trans('Create'):trans('Save'),
    ])
      @slot('title') 
      <a href="{{route('numberings.index')}}"><i class="fas fa-chevron-left"></i></a> {{$mode_title}} {{__('Numbering')}} 
      @endslot
        <div class="form-group row">
            <label for="transaction_type_id" class="col-sm-3 col-form-label">{{__('Transaction Type')}}</label>
            <div class="col-md-9 col-sm-9">
            <select name="transaction_type_id" class="form-control select2 @error('transaction_type_id') is-invalid @enderror" style="width:100%">
                @foreach($types as $type)
                @if($type->id!='journal')
                <option {{$type->id==old('transaction_type_id', $model->trasaction_type_id)?'selected':''}} value="{{$type->id}}">{{tt($type, 'display_name')}}</option>
                @endif
                @endforeach
            </select>
            @error('transaction_type_id') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="name" class="col-sm-3 col-form-label">{{__('Numbering Name')}}</label>
            <div class="col-md-9 col-sm-9">
            <input type="text" required class="form-control @error('name') is-invalid @enderror  @error('display_name') is-invalid @enderror" name="name" id="name" value="{{old('name', $model->name)}}">
            @error('name') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="format" class="col-sm-3 col-form-label">{{__('Numbering Format')}}</label>
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
                    <option {{old('counter_reset', $model->counter_reset)=='y'?'selected':''}} value="y" >{{__('Yearly')}}</option>
                    <option {{old('counter_reset', $model->counter_reset)=='m'?'selected':''}} value="m" >{{__('Monthly')}}</option>
                    <option {{old('counter_reset', $model->counter_reset)=='d'?'selected':''}} value="d" >{{__('Daily')}}</option>
                    <option {{old('counter_reset', $model->counter_reset)=='n'?'selected':''}} value="n" >{{__('No Reset')}}</option>
                </select>
                @error('counter_reset') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="counter_digit" class="col-sm-3 col-form-label">Conter Digit</label>
            <div class="col-md-3 col-sm-2">
                <input type="number" name="counter_digit" class="form-control"  value="{{old('counter_digit', $model->counter_digit)}}" />
                @error('counter_digit') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
            <label for="counter_start" class="col-sm-2 col-form-label">Conter Start</label>
            <div class="col-md-3 col-sm-2">
                <input type="number" name="counter_start" class="form-control" value="{{old('counter_start', $model->counter_start)}}" />
                @error('counter_start') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
    @endcomponent
  </div>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush
@push('js')
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
$(function(){
    $(".select2").select2({theme: 'bootstrap4'});
})
</script>
@endpush