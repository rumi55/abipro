@php 
$active_menu='$company'; 
$mode_title = $mode=='create'?trans('Add'):trans('Edit');
$breadcrumbs = array(
    ['label'=>trans('Company'), 'url'=>route('company.profile')],
    ['label'=>trans('Products'), 'url'=>route('dcru.index', 'products')],
    ['label'=>$mode_title.' '.trans('Product')]
);
@endphp
@extends('layouts.app')
@section('title', $mode_title.' '.trans('Product'))
@section('content')
<div class="row">
    <div class="col-md-12">
    @component('components.card_form', [
        'id'=>'user-form','action'=>$mode=='create'?route('products.create.save'):route('products.edit.update', $product->id),
        'method'=>$mode=='create'?'POST':'PUT',
        'btn_label'=>$mode=='create'?trans('Create'):trans('Save'),

    ])
      @slot('title') 
      <a href="{{route('dcru.index', 'products')}}"><i class="fas fa-chevron-left"></i></a> 
      @if($mode=='create') 
        {{__('New Product')}} 
      @else 
        {{__('Product')}} #{{$product->custom_id}} 
      @endif

      @endslot
      <div class="row">
            <div class="col-md-3">
                @include('components.image', ['fieldname'=>'image', 'image_path'=>$product->image])
                @error('image') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
            <div class="col-md-9">
        <div class="form-group row">
            <label for="custom_id" class="col-sm-2 col-form-label">{{__('ID')}}</label>
            <div class="col-md-10 col-sm-10">
            @if($mode=='create')
            <div class="input-group">
            <select id="numbering_id" name="numbering_id" class="form-control select2 @error('numbering_id') is-invalid @enderror">
                @foreach($numberings as $numbering)
                <option {{$numbering->id==old('numbering_id', $product->numbering_id)?'selected':''}} value="{{$numbering->id}}">{{$numbering->name}}</option>
                @endforeach
                <option {{empty(old('numbering_id', $product->numbering_id))?'selected':''}} value="">{{__('Manual')}}</option>
            </select>
            <input type="text" required class="form-control @error('custom_id') is-invalid @enderror  @error('display_name') is-invalid @enderror" name="custom_id" id="custom_id" value="{{old('custom_id', $product->custom_id)}}" placeholder="{{__('Enter ID')}}">
            </div>
            <small class="text-muted">{{__('Choose numbering format.')}}</small>
            @error('numbering_id')<small class="text-danger">{!!$message!!}</small>@enderror
            @error('custom_id') <small class="text-danger">{!! $message !!}</small> @enderror
            @else
                <input type="text" readonly class="form-control " name="custom_id" id="custom_id" value="{{old('custom_id', $product->custom_id)}}">
            @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="name" class="col-sm-2 col-form-label">{{__('Product Name')}}</label>
            <div class="col-md-10 col-sm-10">
            <input type="text" required class="form-control @error('name') is-invalid @enderror " name="name" id="name" value="{{old('name', $product->name)}}">
            @error('name') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="product_category_id" class="col-sm-2 col-form-label">{{__('Product Category')}}</label>
            <div class="col-md-10 col-sm-10">
                <select id="product_category_id" name="product_category_id" class="form-control select2 @error('product_category_id') is-invalid @enderror">
                    @foreach($categories as $category)
                    <option {{$category->id==old('product_category_id', $product->product_category_id)?'selected':''}} value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
                @error('product_category_id')<small class="text-danger">{!!$message!!}</small>@enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="unit_id" class="col-sm-2 col-form-label">{{__('Unit')}}</label>
            <div class="col-md-10 col-sm-10">
                <select id="unit_id" name="unit_id" class="form-control select2 @error('unit_id') is-invalid @enderror">
                    @foreach($units as $unit)
                    <option {{$unit->id==old('unit_id', $product->unit_id)?'selected':''}} value="{{$unit->id}}">{{$unit->name}}</option>
                    @endforeach
                </select>
                @error('unit_id')<small class="text-danger">{!!$message!!}</small>@enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="buy_price" class="col-sm-2 col-form-label">{{__('Buy Price')}}</label>
            <div class="col-md-4 col-sm-10">
            <input name="buy_price" required type="text" id="buy_price" class="form-control @error('buy_price') is-invalid @enderror" value="{{old('buy_price', $product->buy_price)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
            @error('buy_price')<small class="text-danger">{!!$message!!}</small>@enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="sale_price" class="col-sm-2 col-form-label">{{__('Sale Price')}}</label>
            <div class="col-md-4 col-sm-10">
            <input name="sale_price" required type="text" id="sale_price" class="form-control @error('sale_price') is-invalid @enderror" value="{{old('sale_price', $product->sale_price)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
            @error('sale_price')<small class="text-danger">{!!$message!!}</small>@enderror
            </div>
        </div>
        <div class="form-group row">
            <label for="description" class="col-sm-2 col-form-label">{{__('Description')}}</label>
            <div class="col-md-10 col-sm-10">
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description">{{old('description', $product->description)}}</textarea>
                @error('description') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
        </div>
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
<script type="text/javascript">
$(function(){
    $(".select2").select2({theme: 'bootstrap4'});
    $('[data-mask]').inputmask();
    $('#numbering_id').change(function(){
        if($(this).val()==''){
            $('#custom_id').prop('disabled', false);
            $('#custom_id').val('');
        }else{
            $('#custom_id').prop('disabled', true);
            $('#custom_id').val('[Automatic ID]');
        }
    });
})
</script>
@endpush