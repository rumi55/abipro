@php 
$active_menu='accounts'; 
$breadcrumbs = array(
    ['label'=>trans('Account'), 'url'=>route('accounts.index')],
    ['label'=>trans('Import Accounts')]
);
@endphp
@extends('layouts.app')
@section('title', trans('Import Accounts'))
@section('content')
<div class="row">
    <div class="col-md-12">
    @component('components.card_form', [
        'id'=>'account-form','action'=>route('accounts.import.save'),
        'method'=>'POST',
        'btn_label'=>trans('Import'),
    ])
    Sebelum proses impor, persiapkanlah hal hal berikut:
<ol>
  <li>
    Daftar Akun yang akan diimpor harus dalam bentuk berkas Microsoft Excel (*.xls, *.xlsx)
  </li>
  <li>
    Baris pertama adalah judul kolom dan tidak akan diimpor.
  </li>
  <li>
    Gunakan pemisal desimal dengan titik (.).
  </li>
  <li>
    Kode akun bersifat unik. Jika terdapat kode akun yang sama, maka akan ditimpa dengan akun terbaru.
  </li>
  <li>
    Kolom tipe akun wajib menggunakan kode sebagai berikut:
    <div class="row">
      <div class="col">
        <ul>
          <li>1 = Kas/Bank</li>
          <li>2 = Piutang</li>
          <li>3 = Persediaan</li>
          <li>4 = Aset lancar lainnya</li>
          <li>5 = Aset Tetap</li>
          <li>6 = Akumulasi Depresiasi</li>
          <li>7 = Aset lainnya</li>
          <li>8 = Hutang Usaha</li>
        </ul>
        </div>
        <div class="col">
        <ul>
          <li>9 = Hutang lancar lain-lain</li>
          <li>10 = Hutang jangka panjang</li>
          <li>11 = Modal</li>
          <li>12 = Pendapatan</li>
          <li>13 = Harga Pokok Penjualan</li>
          <li>14 = Beban</li>
          <li>15 = Beban lain-lain</li>
          <li>16 = Pendapatan lain-lain</li>
        </ul>
        </div>
    </div>
  </li>
  <li>Gunakan template excel yang dapat diunduh pada tautan sebagai berikut: <a target="_blank" href="{{asset('/files/chart_of_accounts.xlsx')}}" >Template Akun</a></li>
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
  bsCustomFileInput.init();
    $(".select2").select2({theme: 'bootstrap4'});
    select2Load('#account_parent_id', "{{route('select2', ['name'=>'accounts'])}}")
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