@php 
$active_menu='users'; 
$breadcrumbs = array(
    ['label'=>'Pengguna', 'url'=>route('users.profile')],
    ['label'=>'Edit Profil']
);
@endphp
@extends('layouts.app')
@section('title', 'Pengguna')
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('user._menu', ['active'=>'profile'])
    </div>
    <div class="col-md-9">
    @component('components.card_form', [
        'id'=>'user-form','action'=>route('users.profile.update', $user->id),
        'method'=>'PUT'
    ])
      @slot('title') 
      <a href="{{route('users.profile')}}"><i class="fas fa-chevron-left"></i></a> Edit Profil Pengguna 
      @endslot
        <div class="row">
            <div class="col-md-3">
                @include('components.image', ['fieldname'=>'photo', 'image_path'=>$user->photo])
            </div>
            <div class="col-md-9">
                <div class="form-group">
                    <label for="name" >Nama</label>
                    <input type="text" required class="form-control" name="name" id="name" value="{{old('name', $user->name)}}">
                    @error('name') <small class="text-danger">{!! $message !!}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" required class="form-control" name="email" id="email" value="{{old('email', $user->email)}}">
                    @error('email') <small class="text-danger">{!! $message !!}</small> @enderror
                    
                </div>
                <div class="form-group">
                    <label for="phone">Telepon</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{old('phone', $user->phone)}}">
                    @error('phone') <small class="text-danger">{!! $message !!}</small> @enderror
                </div>
            </div>
        </div>
    @endcomponent
  </div>
</div>
@endsection
