@php
$active_menu='$company';
$mode_title = $mode=='create'?'Tambah':'Edit';
$breadcrumbs = array(
    ['label'=>'Perusahaan', 'url'=>route('company.profile')],
    ['label'=>'Sortir', 'url'=>route('tags.index')],
    ['label'=>$mode_title.' Sortir']
);
@endphp
@extends('layouts.app')
@section('title', 'Tambar Sortir')
@section('content')
<div class="row">
    <div class="col-md-3">
        @include('company._menu', ['active'=>'tags'])
    </div>
    <div class="col-md-9">
    @component('components.card_form', [
        'id'=>'create-form','action'=>$mode=='create'?route('tags.create.save'):route('tags.edit.update', $model->id),
        'method'=>$mode=='create'?'POST':'PUT',
        'btn_label'=>$mode=='create'?'Buat Baru':'Simpan',

    ])
      @slot('title')
      <a href="{{route('tags.index')}}"><i class="fas fa-chevron-left"></i></a> {{$mode_title}} Sortir
      @endslot
        <div class="form-group row">
        <label for="group" class="col-sm-2 col-form-label">{{__('Tag Group')}}</label>
            <div class="col-md-6 col-sm-12">
            <input type="text"  @if($mode=='edit' && $model->isLocked()) readonly @endif autocomplete="off" data-url="{{route('tags.groups')}}" required class="form-control autocomplete @error('group') is-invalid @enderror" name="group" id="group" value="{{old('group', $model->group)}}">
            @error('group') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
        <label for="item_id" class="col-sm-2 col-form-label">{{__('Tag ID')}}</label>
            <div class="col-md-6 col-sm-10">
            <input @if($mode=='edit' && $model->isLocked()) readonly @endif type="text" required class="form-control @error('item_id') is-invalid @enderror  @error('display_name') is-invalid @enderror" name="item_id" id="item_id" value="{{old('item_id', $model->item_id)}}">
            @error('item_id') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
        <label for="item_name" class="col-sm-2 col-form-label">{{__('Tag Name')}}</label>
            <div class="col-md-6 col-sm-10">
            <input type="text" required class="form-control @error('item_name') is-invalid @enderror" name="item_name" id="item_name" value="{{old('item_name', $model->item_name)}}">
            @error('item_name') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
    @endcomponent
  </div>
</div>
@endsection
@push('js')
<script src="https://cdn.jsdelivr.net/gh/xcash/bootstrap-autocomplete@v2.3.5/dist/latest/bootstrap-autocomplete.min.js"></script>
<script>
$(function(){
    // $('.autocomplete').autoComplete({
    //     autoSelect:false,
    //     minLength:2,
    //     preventEnter:true
    // });
})
</script>
@endpush
