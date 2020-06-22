@php 
$active_menu='accounts'; 
$account_name = $account->account_no.' - '.$account->account_name;
$breadcrumbs = array(
    ['label'=>trans('Account'), 'url'=>route('accounts.index')],
    ['label'=>$account_name]
);
@endphp
@extends('layouts.app')

@section('title', $account_name)
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">{{__('Journal Transactions')}}</h5>     
    </div>
    <div class="card-body pb-1">    
      @include('dcru.dtables')
    </div>
</div>
@endsection
@push('js')
<script type="text/javascript">
$(function () {
    $('.btn-delete').click(function(e){
        var form = $(this).parent();
        Swal.fire({
          title: 'Peringatan!',
          icon: 'warning',
          html:'Apakah Anda yakin ingin menghapus akun ini?',
          showCloseButton: false,
          showCancelButton: true,
          focusConfirm: false
        }).then(function(result){
          if(result.value){
            form.submit();
          }
        })
      });
})
</script>
@endpush