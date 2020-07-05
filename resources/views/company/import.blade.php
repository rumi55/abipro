@php 
$active_menu=''; 
$breadcrumbs = array(
    ['label'=>__('Company'), 'url'=>route('company.profile')],
    ['label'=>__('Import Data')]
);
@endphp
@extends('layouts.app')
@section('title', __('Import Data'))
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('company.list_companies')
    </div>
    <div class="col-md-9">
    @component('components.card')
      @slot('title') 
      <a href="{{route('company.profile')}}"><i class="fas fa-chevron-left"></i></a> {{__('Impor Data')}} {{$company->name}}
      @endslot
        <div class="row">
            <div class="col-md-6">
              <h5>{{__('Data Reference')}}</h5>
              <div class="row">
                <div class="col-sm-10">
                  <strong>{{__('Account')}}</strong>
                  <p class="text-muted">
                    Mengimpor semua daftar akun seperti kode akun, nama akun, tipe akun dan akun induk.
                  </p>
                </div>
                <div class="col-sm-2">
                <button class="btn btn-link btn-block btn-import" data-name="account" data-title="{{__('Account')}}" data-toggle="modal" data-target="#modal-import"><i class="fas fa-upload"></i></button>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-10">
                  <strong>{{__('Department')}}</strong>
                  <p class="text-muted">
                    Mengimpor data departemen yang meliputi kode, nama dan keterangan departemen.
                  </p>
                </div>
                <div class="col-sm-2">
                <button class="btn btn-link btn-block btn-import" data-name="department" data-title="{{__('Departments')}}" data-toggle="modal" data-target="#modal-import"><i class="fas fa-upload"></i></button>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-10">
                  <strong>{{__('Contact')}}</strong>
                  <p class="text-muted">
                    Mengimpor data kontak yang meliputi kode, nama, jenis kotak, email, telepon, nomor hp dan alamat.
                  </p>
                </div>
                <div class="col-sm-2">
                <button class="btn btn-link btn-block btn-import" data-name="contact" data-title="{{__('Contacts')}}" data-toggle="modal" data-target="#modal-import"><i class="fas fa-upload"></i></button>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-10">
                  <strong>{{__('Tags')}}</strong>
                  <p class="text-muted">
                    Mengimpor data sortir yang meliputi kode, nama, dan grup sortir.
                  </p>
                </div>
                <div class="col-sm-2">
                <button class="btn btn-link btn-block btn-import" data-name="tags" data-title="{{__('Tags')}}" data-toggle="modal" data-target="#modal-import"><i class="fas fa-upload"></i></button>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <h5>{{__('Data Transaction')}}</h5>
              <div class="row">
                <div class="col-sm-10">
                  <strong>{{__('Voucher')}}</strong>
                  <p class="text-muted">
                    Mengimpor data transaksi voucher yang meliputi tanggal transaksi, nomor transaksi, keterangan, total, debit, dan kredit.
                  </p>
                </div>
                <div class="col-sm-2">
                <button class="btn btn-link btn-block btn-import" data-name="voucher" data-title="{{__('Vouchers')}}" data-toggle="modal" data-target="#modal-import"><i class="fas fa-upload"></i></button>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-10">
                  <strong>{{__('Journal')}}</strong>
                  <p class="text-muted">
                    Mengimpor data transaksi journal yang meliputi tanggal transaksi, nomor transaksi, keterangan, total, debit, dan kredit.
                  </p>
                </div>
                <div class="col-sm-2">
                <button class="btn btn-link btn-block btn-import" data-name="journal" data-title="{{__('Journals')}}" data-toggle="modal" data-target="#modal-import"><i class="fas fa-upload"></i></button>
                </div>
              </div>
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