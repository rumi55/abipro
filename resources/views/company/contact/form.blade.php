@php
$active_menu='$company';
$mode_title = $mode=='create'?trans('Add'):trans('Edit');
$breadcrumbs = array(
    ['label'=>trans('Company'), 'url'=>route('company.profile')],
    ['label'=>trans('Contacts'), 'url'=>route('contacts.index')],
    ['label'=>$mode_title.' '.trans('Contact')]
);
@endphp
@extends('layouts.app')
@section('title', trans('Contact'))
@section('content')
<div class="row">
    <div class="col-md-3">
        @include('company._menu', ['active'=>'contacts'])
    </div>
    <div class="col-md-9">
    @component('components.card_form', [
        'id'=>'user-form','action'=>$mode=='create'?route('contacts.create.save'):route('contacts.edit.update', $model->id),
        'method'=>$mode=='create'?'POST':'PUT',
        'btn_label'=>$mode=='create'?trans('Create'):trans('Save'),

    ])
      @slot('title')
      <a href="{{route('contacts.index')}}"><i class="fas fa-chevron-left"></i></a> {{$mode_title}} {{__('Contact')}}
      @endslot
        <div class="form-group row">
            <label for="custom_id" class="col-sm-2 col-form-label">{{__('ID')}}</label>
            <div class="col-md-10 col-sm-10">
            @if($mode=='create')
            <div class="input-group">
            <div class="input-group-prepend">
            <select id="numbering_id" name="numbering_id" class="form-control select2 @error('numbering_id') is-invalid @enderror">
                @foreach($numberings as $numbering)
                <option {{$numbering->id==old('numbering_id', $model->numbering_id)?'selected':''}} value="{{$numbering->id}}">{{$numbering->name}}</option>
                @endforeach
                <option {{empty(old('numbering_id', $model->numbering_id))?'selected':''}} value="">{{__('Manual')}}</option>
            </select>
            </div>
            <input type="text" required class="form-control @error('custom_id') is-invalid @enderror  @error('display_name') is-invalid @enderror" name="custom_id" id="custom_id" value="{{old('custom_id', $model->custom_id)}}" placeholder="{{__('Enter ID')}}">
            </div>
            @error('numbering_id')<small class="text-danger">{!!$message!!}</small>@enderror
            @error('custom_id') <small class="text-danger">{!! $message !!}</small> @enderror
            <small class="text-muted">{{__('Choose numbering format.')}}</small>
            @else
                <input type="text" readonly class="form-control " name="custom_id" id="custom_id" value="{{old('custom_id', $model->custom_id)}}">
            @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="name" class="col-sm-2 col-form-label">{{__('Name')}}</label>
            <div class="col-md-10 col-sm-10">
            <input type="text" required class="form-control @error('name') is-invalid @enderror " name="name" id="name" value="{{old('name', $model->name)}}">
            @error('name') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="is_customer" class="col-sm-2 col-form-label">{{__('Contact Type')}}</label>
            <div class="col-md-10 col-sm-10">
                <div class="icheck-success d-inline">
                <input id="is_customer" name="is_customer" value="1" type="checkbox" {{old('is_customer', $model->is_customer)?'checked':''}}>
                <label for="is_customer">{{__('Customer')}}</label>
                </div>
                <div class="icheck-success d-inline">
                <input id="is_supplier" name="is_supplier" value="1" type="checkbox" {{old('is_supplier', $model->is_supplier)?'checked':''}}>
                <label for="is_supplier">{{__('Supplier')}}</label>
                </div>
                <div class="icheck-success d-inline">
                <input id="is_employee" name="is_employee" value="1" type="checkbox" {{old('is_employee', $model->is_employee)?'checked':''}}>
                <label for="is_employee">{{__('Employee')}}</label>
                </div>
                <div class="icheck-success d-inline">
                <input id="is_others" name="is_others" value="1" type="checkbox" {{old('is_others', $model->is_others)?'checked':''}}>
                <label for="is_others">{{__('Others')}}</label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label for="email" class="col-sm-2 col-form-label">{{__('Email')}}</label>
            <div class="col-md-10 col-sm-10">
            <input type="email" class="form-control @error('email') is-invalid @enderror " name="email" id="email" value="{{old('email', $model->email)}}">
            @error('email') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="phone" class="col-sm-2 col-form-label">{{__('Phone')}}</label>
            <div class="col-md-10 col-sm-10">
            <input type="text" class="form-control @error('phone') is-invalid @enderror " name="phone" id="phone" value="{{old('phone', $model->phone)}}">
            @error('phone') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="mobile" class="col-sm-2 col-form-label">{{__('Mobile Phone')}}</label>
            <div class="col-md-10 col-sm-10">
            <input type="text" class="form-control @error('mobile') is-invalid @enderror " name="mobile" id="mobile" value="{{old('mobile', $model->mobile)}}">
            @error('mobile') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="address" class="col-sm-2 col-form-label">{{__('Address')}}</label>
            <div class="col-md-10 col-sm-10">
                <textarea class="form-control @error('address') is-invalid @enderror" name="address" id="address">{{old('address', $model->address)}}</textarea>
                @error('address') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="account_payable" class="col-sm-2 col-form-label">{{__('Account Payable')}}</label>
            <div class="col-md-6 col-sm-10">
                <select class="form-control account @error('account_payable') is-invalid @enderror " name="account_payable" id="account_payable" data-value="{{old('account_payable', $model->account_payable)}}"></select>
                @error('account_payable') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
            <label for="opening_balance_ap" class="col-sm-2 col-form-label">{{__('Opening Balance')}}</label>
            <div class="col-md-2 col-sm-10">
                <input name="opening_balance_ap" type="text" id="opening_balance_ap" class="form-control number @error('opening_balance_ap') is-invalid @enderror" value="{{old('opening_balance_ap', empty($model->opening_balance_ap)?'0,00':$model->opening_balance_ap)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
            </div>
        </div>
        <div class="form-group row">
            <label for="account_receivable" class="col-sm-2 col-form-label">{{__('Account Receivable')}}</label>
            <div class="col-md-6 col-sm-10">
            <select class="form-control account @error('account_receivable') is-invalid @enderror " name="account_receivable" id="account_receivable" data-value="{{old('account_receivable', $model->account_receivable)}}"></select>
            @error('account_receivable') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
            <label for="opening_balance_ar" class="col-sm-2 col-form-label">{{__('Opening Balance')}}</label>
            <div class="col-md-2 col-sm-10">
                <input name="opening_balance_ar" type="text" id="opening_balance_ar" class="form-control number @error('opening_balance_ar') is-invalid @enderror" value="{{old('opening_balance_ar', empty($model->opening_balance_ar)?'0,00':$model->opening_balance_ar)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
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
<script src="{{asset('plugins/inputmask/min/jquery.inputmask.bundle.min.js')}}"></script>
<script src="{{asset('js/select.js')}}"></script>
<script type="text/javascript">
$(function(){
    loadSelect();
    $('.currency').inputmask({ 'alias': 'currency' })
    $('[data-mask]').inputmask();
    $('#numbering_id').change(function(){
        if($(this).val()==''){
            $('#custom_id').prop('disabled', false);
            $('#custom_id').val('');
        }else{
            $('#custom_id').prop('disabled', true);
            $('#custom_id').val('[{{__("Automatic ID")}}]');
        }
    });
})
</script>
@endpush
