@php 
$active_menu=''; 
$breadcrumbs = array(
    ['label'=>__('Company'), 'url'=>route('company.profile')],
    ['label'=>__('Transfer Data')]
);
@endphp
@extends('layouts.app')
@section('title', __('Transfer Data'))
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('company.list_companies')
    </div>
    <div class="col-md-9">
    @component('components.card_form', [
        'id'=>'company-form','action'=>route('companies.transfer'),
        'method'=>'POST', 'btn_label'=>__('Submit')
    ])
      @slot('title') 
      <a href="{{route('companies.index')}}"><i class="fas fa-chevron-left"></i></a> {{__('Transfer Data')}} {{$company->name}}
      @endslot
      <div class="callout callout-info">
        <p>Proses ini akan menggabungkan data dari sumber data perusahaan terpilih.</p>
      </div>
      <div class="form-group">
        <label>{{__('Data Source')}}</label>
        <select class="form-control select2" name="company_id">
        @foreach($companies as $comp)
          @if($comp->id!=$company->id)
            <option value="{{$comp->id}}">{{$comp->name}}</option>
          @endif
        @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>{{__('Entity')}}</label>
        <select class="form-control select2" name="type">
        <option value="account">{{__('Chart of Accounts')}}</option>
        <option value="department">{{__('Departments')}}</option>
        <option value="contact">{{__('Contacts')}}</option>
        <option value="tags">{{__('Tags')}}</option>
        <option value="voucher">{{__('Vouchers')}}</option>
        <option value="journal">{{__('Journals')}}</option>
        </select>
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
$(function(){
    $(".select2").select2({theme: 'bootstrap4'});
})
</script>
@endpush