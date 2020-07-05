@php 
$active_menu='company'; 
$breadcrumbs = array(
    ['label'=>'Company']
);
@endphp
@extends('layouts.app')
@section('title', trans('Company'))
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-3">
      @include('company._menu', ['active'=>'profile'])
    </div>
    <div class="col-md-9">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">{{__('Company Profile')}}</h3>
          <div class="card-tools">
          <a href="{{route('company.profile.edit')}}" class="btn btn-tool" title="Edit Profil"><i class="fa fa-edit"></i></a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3 col-sm-12">
              <div class="text-center">
                <img class="profile-user-img img-fluid img-circle" src="{{url_image($company->logo)}}" alt="Logo">
              </div>
              <h3 class="profile-username text-center">{{$company->name}}</h3>
            </div>
            <div class="col-md-9 col-sm-12">
              <strong><i class="fas fa-map-marker-alt mr-1"></i> Alamat</strong>
              <p class="text-muted">
                {{empty($company->address)?'-':$company->address}}
              </p>
              <hr>
              <strong><i class="fas fa-map-marker-alt mr-1"></i> Alamat Pengiriman</strong>
              <p class="text-muted">
                {{empty($company->shipping_address)?'-':$company->shipping_address}}
              </p>
              <hr>
              <strong><i class="fas fa-phone mr-1"></i> Telepon</strong>
              <p class="text-muted">{{empty($company->phone)?'-':$company->phone}}</p>
              <hr>
              <strong><i class="fas fa-fax mr-1"></i> Fax</strong>
              <p class="text-muted">{{empty($company->fax)?'-':$company->fax}}</p>
              <hr>
              <strong><i class="far fa-envelope mr-1"></i> Email</strong>
              <p class="text-muted">{{empty($company->email)?'-':$company->email}}</p>
              <hr>
              <strong><i class="far fa-globe mr-1"></i> Website</strong>
              <p class="text-muted">{{empty($company->website)?'-':$company->website}}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection