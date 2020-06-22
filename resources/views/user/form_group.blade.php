t@php 
$active_menu='users'; 
$mode_title = $mode=='create'?'Buat':'Edit';
$breadcrumbs = array(
    ['label'=>'Pengguna', 'url'=>route('users.profile')],
    ['label'=>'Grup Pengguna', 'url'=>route('user_groups.index')],
    ['label'=>$mode_title.' Grup Pengguna']
);
@endphp
@extends('layouts.app')
@section('title', 'Pengguna')
@section('content')
<div class="row">
    <div class="col-md-3">
        @include('user._menu', ['active'=>'groups'])
    </div>
    <div class="col-md-9">
    @component('components.card_form', [
        'id'=>'user-form','action'=>$mode=='create'?route('user_groups.create.save'):route('user_groups.edit.update', $group->id),
        'method'=>$mode=='create'?'POST':'PUT',
        'btn_label'=>$mode=='create'?'Buat Baru':'Simpan'
    ])
      @slot('title') 
      <a href="{{route('user_groups.index')}}"><i class="fas fa-chevron-left"></i></a> {{$mode_title}} Grup Pengguna 
      @endslot
        <div class="form-group row">
            <label for="name" class="col-sm-2 col-form-label">Nama Grup</label>
            <div class="col-md-10 col-sm-10">
            <input type="text" required class="form-control @error('name') is-invalid @enderror  @error('display_name') is-invalid @enderror" name="display_name" id="display_name" value="{{old('display_name', $group->display_name)}}">
            @error('display_name') <small class="text-danger">{!! $message !!}</small> @enderror
            @error('name') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="description" class="col-sm-2 col-form-label">Keterangan</label>
            <div class="col-md-10 col-sm-10">
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" >{{old('description', $group->description)}}</textarea>
                @error('description') <small class="text-danger">{!! $message !!}</small> @enderror
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
$(function () {
    $(".select2").select2({theme: 'bootstrap4'});
});

</script>
@endpush