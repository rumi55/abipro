@php 
$active_menu='users'; 
$breadcrumbs = array(
    ['label'=>'Pengguna', 'url'=>route('users.index')],
    ['label'=>($mode=='create'?'Buat ':'Edit').' Pengguna']
);
@endphp
@extends('layouts.app')
@section('title', 'Pengguna')
@section('content')
<div class="row">
    <div class="col-md-3">
        @include('user._menu', ['active'=>'users'])
    </div>
    <div class="col-md-9">
    @component('components.card_form', [
        'id'=>'user-form','action'=>$mode=='create'?route('users.create.save'):route('users.edit.update', $user->id),
        'method'=>$mode=='create'?'POST':'PUT',
        'btn_label'=>$mode=='create'?'Buat Baru':'Simpan',
        
    ])
      @slot('title') 
      <a href="{{route('users.index')}}"><i class="fas fa-chevron-left"></i></a> {{$mode=='create'?'Buat':'Edit'}} Pengguna 
      @endslot
        <div class="form-group row">
            <label for="name" class="col-sm-2 col-form-label">Nama</label>
            <div class="col-md-10 col-sm-10">
            <input type="text" required class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{old('name', $user->name)}}">
            @error('name') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="email" class="col-sm-2 col-form-label">Email</label>
            <div class="col-md-10 col-sm-10">
                <input type="email" required class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{old('email', $user->email)}}">
                @error('email') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="phone" class="col-sm-2 col-form-label">Nomor HP</label>
            <div class="col-md-10 col-sm-10">
                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{old('phone', $user->phone)}}">
                @error('phone') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="user_group_id" class="col-sm-2 col-form-label">Grup Pengguna</label>
            <div class="col-md-10 col-sm-10">
                <select class="form-control select2 @error('user_group_id') is-invalid @enderror" id="user_group_id" name="user_group_id" value="{{old('phone', $user->phone)}}">
                @foreach($groups as $group)
                <option {{$group->id==old('user_group_id', ($mode!='create'?($user->userGroup()!=null?$user->userGroup()->user_group_id:''):''))?'selected':''}} value="{{$group->id}}">{{$group->display_name}}</option>
                @endforeach
                </select>
                @error('user_group_id') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        @if($mode=='create')
        <div class="form-group row">
            <label for="password" class="col-sm-2 col-form-label">Password</label>
            <div class="col-md-10 col-sm-10">
                <input type="text" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="">
                @error('password') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        @endif
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