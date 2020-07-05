@php 
$active_menu=''; 
$breadcrumbs = array(
    ['label'=>trans('Company')]
);
@endphp
@extends('layouts.app')
@section('content-header-right')
  @if($user->is_owner)
  <a href="{{route('company.register')}}" class="btn btn-primary" ><i class="fas fa-plus"></i> {{__('Create Company')}}</a>
  @endif
@endsection
@section('title', trans('Company'))
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-3">
      <div class="card">
        <div class="card-body p-0">
          <ul class="nav nav-pills flex-column">
          @foreach($companies as $i=> $company)
            <li class="nav-item">
              <a href="#comp-{{$company->id}}" class="nav-link {{empty(request('id'))?($i==0?' active':''):(request('id')==$company->id?' active':'')}}" data-toggle="tab">
              {{$company->name}}
              @if($company->is_active)<span class="float-right badge badge-success">{{__('Active')}}</span>@endif
              </a>
            </li>
          @endforeach
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="tab-content p-0">
        @foreach($companies as $i=> $company)
        <div id="comp-{{$company->id}}" class="tab-pane {{empty(request('id'))?($i==0?' active':''):(request('id')==$company->id?' active':'')}}">
          @include('company._profile', ['company'=>$company])
        </div>      
        @endforeach
      </div>      
    </div>
  </div>
</div>
@endsection