@php 
$active_menu='reports'; 
$breadcrumbs = array(
    ['label'=>'Perusahaan', 'url'=>route('company.profile')],
    ['label'=>'Edit Profil Perusahaan']
);
@endphp
@extends('layouts.app')
@section('title', 'Perusahaan')
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('company._menu', ['active'=>'profile'])
    </div>
    <div class="col-md-9">
    @component('components.card_form', [
        'id'=>'company-form','action'=>route('company.profile.update'),
        'method'=>'PUT'
    ])
      @slot('title') 
      <a href="{{route('company.profile')}}"><i class="fas fa-chevron-left"></i></a> Edit Profil Perusahaan 
      @endslot
        <div class="row">
            <div class="col-md-3">
                @include('components.image', ['fieldname'=>'logo', 'image_path'=>$company->logo])
                @error('logo') <small class="text-danger">{!! $message !!}</small> @enderror
            </div>
            <div class="col-md-9">
                <div class="form-group">
                    <label for="name" >Nama Perusahaan</label>
                    <input type="text" required class="form-control" name="name" id="name" value="{{old('name', $company->name)}}">
                    @error('name') <small class="text-danger">{!! $message !!}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="address">Alamat</label>
                    <textarea class="form-control" name="address" id="address" >{{old('address', $company->address)}}</textarea>
                    @error('shipping_addres') <small class="text-danger">{!! $message !!}</small> @enderror  
                </div>
                <div class="form-group">
                    <label for="shipping_address">Alamat Pengiriman</label>
                    <textarea class="form-control" name="shipping_address" id="shipping_address" >{{old('shipping_address', $company->shipping_address)}}</textarea>
                    @error('shipping_address') <small class="text-danger">{!! $message !!}</small> @enderror  
                </div>
                
                <div class="form-group">
                    <label for="phone">Telepon</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{old('phone', $company->phone)}}">
                    @error('phone') <small class="text-danger">{!! $message !!}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="fax">Fax</label>
                    <input type="text" class="form-control" id="fax" name="fax" value="{{old('fax', $company->fax)}}">
                    @error('fax') <small class="text-danger">{!! $message !!}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" value="{{old('email', $company->email)}}">
                    @error('email') <small class="text-danger">{!! $message !!}</small> @enderror  
                </div>
                <div class="form-group">
                    <label for="website">Website</label>
                    <input type="text" class="form-control" id="website" name="website" value="{{old('website', $company->website)}}">
                    @error('website') <small class="text-danger">{!! $message !!}</small> @enderror
                </div>
            </div>
        </div>
    @endcomponent
  </div>
</div>
@endsection
@push('js')
<script>
    function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#photo-img').attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]); // convert to base64 string
  }
}
$(function(){
$("#photo").change(function() {
  readURL(this);
});
$('#btn-img').click(function(){$('#photo').trigger('click')})
})
</script>
@endpush