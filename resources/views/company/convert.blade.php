@php 
$active_menu=''; 
$breadcrumbs = array(
    ['label'=>__('Company'), 'url'=>route('company.profile')],
    ['label'=>__('Convert Abipro Desktop')]
);
@endphp
@extends('layouts.app')
@section('title', __('Convert Abipro Desktop'))
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('company.list_companies')
    </div>
    <div class="col-md-9">
    @component('components.card')
      @slot('title') 
      <a href="{{route('companies.index')}}"><i class="fas fa-chevron-left"></i></a> {{__('Convert Abipro Desktop')}} to {{$company->name}}
      @endslot
        <div class="row">
            <div class="col-md-12">
              <p>
              Proses ini akan mengkonversi data Abipro Desktop ke dalam Abipro versi Web. Siapkan berkas-berkas berikut ini:
              gltype.dbf, glnama.dbf, glmast.dbf
              </p>
              <table class="table">
              <thead>
                <tr>
                  <th>Abipro Desktop</th>
                  <th><i class="fas fa-arrow-right"></i></th>
                  <th>Abipro Web</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Tipe Perkiraan (gltype.dbf)</td>
                  <td><i class="fas fa-arrow-right"></i></td>
                  <td>Akun (level 1)</td>
                  <td class="text-right"><a href="{{route('companies.convert', 'gltype')}}" title="Convert"><i class="fas fa-cloud-upload-alt"></i></a></td>
                </tr>
                <tr>
                  <td>Perkiraan (glnama.dbf)</td>
                  <td><i class="fas fa-arrow-right"></i></td>
                  <td>Akun (level 2)</td>
                  <td class="text-right"><a href="#" title="Convert"><i class="fas fa-cloud-upload-alt"></i></a></td>
                </tr>
              </tbody>
              </table>
            </div>
        </div>
    @endcomponent
  </div>
</div>

@component('components.modal_form', ['id'=>'modal-import', 'action'=>route('companies.import'), 'btn_label'=>__('Submit')])
    @slot('title')
      Import <span id="import-title">Data</span>
    @endslot
    Silakan pilih berkas yang akan diimpor:
    <div class="input-group">
        <div class="custom-file">
          <input name="name" id="import-name" type="hidden" />
          <input  required type="file" class="form-control custom-file-input @error('file') is-invalid @enderror" name="file" id="file" placeholder="" value="">
          <label class="custom-file-label" for="file">Pilih File</label>
        </div>
    </div>
    <small class="text-muted">Gunakan template di <a href="">sini</a>.</small>
@endcomponent
@endsection

@push('js')
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
<script type="text/javascript">
$(function () {
  bsCustomFileInput.init();
  $('.btn-import').click(function(e){
    $('#import-name').val($(this).attr('data-name'))
    $('#import-title').html($(this).attr('data-title'))
    // $('#modal-import').modal();
  })
});
</script>
@endpush