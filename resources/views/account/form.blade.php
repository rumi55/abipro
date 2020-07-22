@php
$active_menu='accounts';
$breadcrumbs = array(
    ['label'=>trans('Account'), 'url'=>route('accounts.index')],
    ['label'=>($mode=='create' || $mode=='add_child')?trans('New Account'):trans('Edit Account')]
);
@endphp
@extends('layouts.app')
@section('title', ($mode=='create' || $mode=='add_child')?trans('New Account'):trans('Edit Account'))
@section('content')
<div class="row">
    <div class="col-md-12">
    @component('components.card_form', [
        'id'=>'account-form','action'=>($mode=='create' || $mode=='add_child')?route('accounts.create.save'):route('accounts.edit.update', $account->id),
        'method'=>($mode=='create' || $mode=='add_child')?'POST':'PUT',
        'btn_label'=>($mode=='create' || $mode=='add_child')?trans('Create Account'):trans('Save'),
    ])

        <div class="form-group row">
            <label for="account_type_id" class="col-sm-2 col-form-label">{{__('Account Type')}}</label>
            <div class="col-md-10 col-sm-10">
            @if($mode=='create' || ($mode=='edit' && $account->tree_level==0 && !$account->isLocked()))
            <select class="form-control select2 @error('account_type_id') is-invalid @enderror" id="account_type_id" name="account_type_id" value="{{old('account_type_id', $account->account_type_id)}}">
                @foreach($account_types as $type)
                <option {{$type->id==old('account_type_id', $account->account_type_id)?'selected':''}} value="{{$type->id}}">{{tt($type,'name')}}</option>
                @endforeach
            </select>
            @error('account_type_id') <small class="text-danger">{!! $message !!}</small> @enderror
            @else
            <input readonly tabindex="-1" class="form-control-plaintext"  value="{{$account->accountType->name}}">
            <input type="hidden" name="account_type_id"  value="{{$account->account_type_id}}">
            @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="account_no" class="col-sm-2 col-form-label">{{__('Account No.')}}</label>
            <div class="col-md-10 col-sm-10">
            <input type="text" {{($mode=='edit' && $account->isLocked())?'readonly':''}} required class="form-control{{($mode=='edit' && $account->isLocked())?'-plaintext':''}} @error('account_no') is-invalid @enderror" name="account_no" id="account_no" value="{{old('account_no', $account->account_no)}}">
            @error('account_no') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="account_name" class="col-sm-2 col-form-label">{{__('Account Name')}}</label>
            <div class="col-md-10 col-sm-10">
            <input type="text" required class="form-control @error('account_name') is-invalid @enderror" name="account_name" id="account_name" value="{{old('account_name', $account->account_name)}}">
            @error('account_name') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        @if(($account->parent!=null && $mode=='edit') || $mode=='create')
        <div class="form-group row">
            <label for="account_parent_id" class="col-sm-2 col-form-label">{{__('Account Parent')}}</label>
            <div class="col-md-10 col-sm-10">
            @if($mode=='create')
            <select class="form-control select2 @error('account_parent_id') is-invalid @enderror" id="account_parent_id" name="account_parent_id" data-selected="{{old('account_parent_id', $account->account_parent_id)}}"></select>
            @error('account_parent_id') <small class="text-danger">{!! $message !!}</small> @enderror
            @else
            <input readonly tabindex="-1" class="form-control-plaintext"  value="{{'('.$account->parent->account_no.') '.$account->parent->account_name}}">
            <input type="hidden" name="account_parent_id"  value="{{$account->account_parent_id}}">
            @endif
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
var accounts = [];
function select2Load(selector, url, data={}){
  $.ajax({
    url: url,
    dataType: 'json',
    data: data,
    success: function(res){
        accounts = res;
        var data = accounts.filter(function(item){return item.account_type_id==$('#account_type_id').val()});
      $(selector).select2({theme: 'bootstrap4',data:data});
      var val = $(selector).attr('data-selected');
      if($(selector).prop('multiple') && val!=""){
        $(selector).val(val.split(','));
      }else{
        $(selector).val(val);
      }
      $(selector).trigger('change');
    }
  })
}
$(function () {
    $(".select2").select2({theme: 'bootstrap4'});
    select2Load('#account_parent_id', "{{route('select2', ['name'=>'accounts'])}}", {tree_level:0})
    $('#account_type_id').change(function(){
        var v = $(this).val();
        var data = accounts.filter(function(item){return item.account_type_id==$('#account_type_id').val()});
        $('#account_parent_id').val(null).empty().select2('destroy');
        $('#account_parent_id').select2({theme: 'bootstrap4',data:data});
        $('#account_parent_id').val(null).trigger('change');
    });
});
</script>
@endpush
