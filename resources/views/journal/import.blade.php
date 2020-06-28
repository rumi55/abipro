@php 
$active_menu='journals'; 
$breadcrumbs = array(
    ['label'=>trans('Journal'), 'url'=>route('dcru.index','journals')],
    ['label'=>trans('Import Journals')]
);
@endphp
@extends('layouts.app')
@section('title', trans('Import Journals'))
@section('content')
<div class="row">
    <div class="col-md-12">
    @component('components.card_form', [
        'id'=>'journal-form','action'=>route('journals.import.save'),
        'method'=>'POST',
        'btn_label'=>trans('Import'),
    ])
    Sebelum proses impor, persiapkanlah hal hal berikut:
<ol>
  <li>
    Jurnal yang akan diimpor harus dalam bentuk berkas Microsoft Excel (*.xls, *.xlsx) sesuai format pada template ini.
  </li>
  <li>
    Baris pertama adalah judul kolom dan tidak akan diimpor.
  </li>
  <li>
    Gunakan pemisal desimal dengan titik (.).
  </li>
  <li>
    Silakan pilih berkas yang akan diimpor:
    <div class="input-group">
        <div class="custom-file">
            <input  required type="file" class="form-control custom-file-input @error('file') is-invalid @enderror" name="file" id="file" placeholder="" value="">
            <label class="custom-file-label" for="file">Pilih File</label>
        </div>
    </div>
  </li>
</ol>
    @endcomponent
  </div>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush
@push('js')
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
$(function () {
  bsCustomFileInput.init();
});
</script>
@endpush