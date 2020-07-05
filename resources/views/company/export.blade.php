@php 
$active_menu=''; 
$breadcrumbs = array(
    ['label'=>__('Company'), 'url'=>route('company.profile')],
    ['label'=>__('Export Data')]
);
@endphp
@extends('layouts.app')
@section('title', __('Export Data'))
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('company.list_companies')
    </div>
    <div class="col-md-9">
    @component('components.card')
      @slot('title') 
      <a href="{{route('company.profile')}}"><i class="fas fa-chevron-left"></i></a> {{__('Export Data')}} {{$company->name}}
      @endslot
        <div class="row">
            <div class="col-md-6">
              <h5>{{__('Data Reference')}}</h5>
              <div class="row">
                <div class="col-sm-10">
                  <strong>{{__('Account')}}</strong>
                  <p class="text-muted">
                    Mengekspor semua daftar akun seperti kode akun, nama akun, tipe akun dan akun induk.
                  </p>
                </div>
                <div class="col-sm-2">
                <a href="{{route('companies.export.excel', ['name'=>'account'])}}" class="btn btn-link btn-block"><i class="fas fa-download"></i></a>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-10">
                  <strong>{{__('Department')}}</strong>
                  <p class="text-muted">
                    Mengekspor data departemen yang meliputi kode, nama dan keterangan departemen.
                  </p>
                </div>
                <div class="col-sm-2">
                <a href="{{route('companies.export.excel', ['name'=>'department'])}}" class="btn btn-link btn-block"><i class="fas fa-download"></i></a>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-10">
                  <strong>{{__('Contact')}}</strong>
                  <p class="text-muted">
                    Mengekspor data kontak yang meliputi kode, nama, jenis kotak, email, telepon, nomor hp dan alamat.
                  </p>
                </div>
                <div class="col-sm-2">
                <a href="{{route('companies.export.excel', ['name'=>'contact'])}}" class="btn btn-link btn-block"><i class="fas fa-download"></i></a>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-10">
                  <strong>{{__('Tags')}}</strong>
                  <p class="text-muted">
                    Mengekspor data sortir yang meliputi kode, nama, dan grup sortir.
                  </p>
                </div>
                <div class="col-sm-2">
                <a href="{{route('companies.export.excel', ['name'=>'tags'])}}" class="btn btn-link btn-block"><i class="fas fa-download"></i></a>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <h5>{{__('Data Transaction')}}</h5>
              <div class="row">
                <div class="col-sm-10">
                  <strong>{{__('Voucher')}}</strong>
                  <p class="text-muted">
                    Mengekspor data transaksi voucher yang meliputi tanggal transaksi, nomor transaksi, keterangan, total, debit, dan kredit.
                  </p>
                </div>
                <div class="col-sm-2">
                <a href="{{route('companies.export.excel', ['name'=>'voucher'])}}" class="btn btn-link btn-block"><i class="fas fa-download"></i></a>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-10">
                  <strong>{{__('Journal')}}</strong>
                  <p class="text-muted">
                    Mengekspor data transaksi journal yang meliputi tanggal transaksi, nomor transaksi, keterangan, total, debit, dan kredit.
                  </p>
                </div>
                <div class="col-sm-2">
                <a href="{{route('companies.export.excel', ['name'=>'journal'])}}" class="btn btn-link btn-block"><i class="fas fa-download"></i></a>
                </div>
              </div>
            </div>
        </div>
    @endcomponent
  </div>
</div>
@endsection