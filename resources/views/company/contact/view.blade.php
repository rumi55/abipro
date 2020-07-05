@php 
$active_menu='company'; 
$breadcrumbs = array(
    ['label'=>'Company']
);
@endphp
@extends('layouts.app')
@section('title', trans('Detail Contact'))
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-3">
      @include('company._menu', ['active'=>'contacts'])
    </div>
    <div class="col-md-9">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><a href="{{route('contacts.index')}}"><i class="fas fa-chevron-left"></i></a> {{__('Contact')}} #{{$contact->custom_id}}</h3>
          <div class="card-tools">
          <a href="{{route('contacts.edit', $contact->id)}}" class="btn btn-tool" title="{{__('Edit')}}"><i class="fa fa-edit"></i></a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 col-sm-12">
              <strong><i class="fas fa-map-marker-alt mr-1"></i> {{__('Name')}}</strong>
              <p class="text-muted">
                {{$contact->name}}
              </p>
              <hr>
              <strong><i class="fas fa-map-marker-alt mr-1"></i> {{__('Address')}}</strong>
              <p class="text-muted">
                {{empty($contact->address)?'-':$contact->address}}
              </p>
              <hr>
              <strong><i class="fas fa-phone mr-1"></i> {{__('mobile')}}</strong>
              <p class="text-muted">{{empty($contact->mobile)?'-':$contact->mobile}}</p>
              <hr>
              <strong><i class="far fa-envelope mr-1"></i> Email</strong>
              <p class="text-muted">{{empty($contact->email)?'-':$contact->email}}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
