@php 
$active_menu=''; 
$breadcrumbs = array(
    ['label'=>__('Company'), 'url'=>route('company.profile')],
    ['label'=>__('Delete Company')]
);
@endphp
@extends('layouts.app')
@section('title', __('Company'))
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('company.list_companies')
    </div>
    <div class="col-md-9">
    @component('components.card_form', [
        'id'=>'company-form','action'=>route('companies.delete', $company->id),
        'method'=>'POST', 'btn_label'=>__('Import')
    ])
      @slot('title') 
      <a href="{{route('company.profile')}}"><i class="fas fa-chevron-left"></i></a> {{__('Import Data to ')}} {{$company->name}}
      @endslot
        <div class="row">
            <div class="col-md-12">
            <p>This action cannot be undone. This will permanently delete all of company data, accounts, vouchers, journals, contacts, and other relate data.</p>
            <p>Please type your password to continue this action.</p>
            <div class="form-group">
            <input type="password" class="form-control" name="password" required placeholder="Type your password here" />
            </div>
                
            </div>
        </div>
    @endcomponent
  </div>
</div>
@endsection