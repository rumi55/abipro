@php
$active_menu='company';
$breadcrumbs = array(
    ['label'=>trans('Company'), 'url'=>route('company.profile')],
    ['label'=>trans('Contacts')]
);
@endphp
@section('content-header-right')
  <a href="{{route('contacts.create')}}" class="btn btn-primary" ><i class="fas fa-plus"></i> {{__('Add')}} {{__('Contact')}}</a>
@endsection
@extends('layouts.app')
@section('title', trans('Contacts'))
@section('content')

<div class="row">
    <div class="col-md-3">
      @include('company._menu', ['active'=>'contacts'])
    </div>
    <div class="col-md-9">
        <div class="card card-outline card-primary card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                <li class="nav-item">
                <a class="nav-link active" id="custom-tabs-three-customer-tab" data-toggle="pill" href="#custom-tabs-three-customer" role="tab" aria-controls="custom-tabs-three-customer" aria-selected="true">{{__('Customers')}}</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" id="custom-tabs-three-supplier-tab" data-toggle="pill" href="#custom-tabs-three-supplier" role="tab" aria-controls="custom-tabs-three-supplier" aria-selected="false">{{__('Suppliers')}}</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" id="custom-tabs-three-employee-tab" data-toggle="pill" href="#custom-tabs-three-employee" role="tab" aria-controls="custom-tabs-three-employee" aria-selected="false">{{__('Employee')}}</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" id="custom-tabs-three-others-tab" data-toggle="pill" href="#custom-tabs-three-others" role="tab" aria-controls="custom-tabs-three-others" aria-selected="false">{{__('Others')}}</a>
                </li>
            </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-three-tabContent">
                    <div class="tab-pane fade active show" id="custom-tabs-three-customer" role="tabpanel" aria-labelledby="custom-tabs-three-customer-tab">
                    @include('dcru.dtables_js')
                    @include('dcru.dtables_script', $dtcustomer)
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-three-supplier" role="tabpanel" aria-labelledby="custom-tabs-three-supplier-tab">
                    @include('dcru.dtables_script', $dtsupplier)
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-three-employee" role="tabpanel" aria-labelledby="custom-tabs-three-employee-tab">
                    @include('dcru.dtables_script', $dtemployee)
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-three-others" role="tabpanel" aria-labelledby="custom-tabs-three-others-tab">
                    @include('dcru.dtables_script', $dtothers)
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
