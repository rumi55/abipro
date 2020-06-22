@php 
$active_menu='$company'; 
$mode_title = $mode=='create'?trans('Add'):trans('Edit');
$breadcrumbs = array(
    ['label'=>'Company', 'url'=>route('company.profile')],
    ['label'=>'Department', 'url'=>route('departments.index')],
    ['label'=>$mode_title.' '.trans('Department')]
);
@endphp
@extends('layouts.app')
@section('title', trans('Department'))
@section('content')
<div class="row">
    <div class="col-md-3">
        @include('company._menu', ['active'=>'departments'])
    </div>
    <div class="col-md-9">
    @component('components.card_form', [
        'id'=>'user-form','action'=>$mode=='create'?route('departments.create.save'):route('departments.edit.update', $model->id),
        'method'=>$mode=='create'?'POST':'PUT',
        'btn_label'=>$mode=='create'?trans('Create'):trans('Save'),

    ])
      @slot('title') 
      <a href="{{route('departments.index')}}"><i class="fas fa-chevron-left"></i></a> {{__($mode_title)}} {{__('Department')}}
      @endslot
        <div class="form-group row">
            <label for="custom_id" class="col-sm-2 col-form-label">{{__('ID')}}</label>
            <div class="col-md-4 col-sm-6">
            <input type="text" required class="form-control @error('custom_id') is-invalid @enderror  @error('display_name') is-invalid @enderror" name="custom_id" id="custom_id" value="{{old('custom_id', $model->custom_id)}}">
            @error('custom_id') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="name" class="col-sm-2 col-form-label">{{__('Department')}}</label>
            <div class="col-md-10 col-sm-10">
            <input type="text" required class="form-control @error('name') is-invalid @enderror  @error('display_name') is-invalid @enderror" name="name" id="name" value="{{old('name', $model->name)}}">
            @error('name') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="description" class="col-sm-2 col-form-label">{{__('Description')}}</label>
            <div class="col-md-10 col-sm-10">
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description">{{old('description', $model->description)}}</textarea>
                @error('description') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
    @endcomponent
  </div>
</div>
@endsection