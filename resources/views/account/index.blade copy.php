@php 
$active_menu='accounts'; 
$breadcrumbs = array(
    ['label'=>'Akun']
);
@endphp
@extends('layouts.app')
@section('title', 'Akun')
@section('content-header-right')
<a href="{{route('accounts.create')}}" class="btn btn-primary" ><i class="fas fa-plus"></i> Tambah Akun</a>
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Daftar Akun</h5>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-toggle="dropdown">
            <i class="fas fa-th"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" role="menu">
                <a href="{{route('accounts.opening_balance')}}" class="dropdown-item">Saldo Awal</a>
                <a href="{{route('accounts.budgets')}}" class="dropdown-item">Anggaran</a>
            </div>
        </div>
    </div>
    <div class="card-body pb-1">    
        <div class="table-responsive mt-4">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th style="width:50px">
                        </th>
                        <th>Kode</th>
                        <th>Akun</th>
                        <th>Tipe</th>
                        <th>Saldo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($accounts as $account)
                    <tr id="row-{{$account->id}}" class="tr-row" data-id="{{$account->id}}" data-parent="{{$account->account_parent_id}}">
                        <td>
                            @if($account->has_children)
                            <a class="collapse-btn" href="javascript:void(0)" data-id="{{$account->id}}" data-collapse="false">
                                </small><i class="fas fa-minus"></i></small>
                            </a>
                            @endif
                        </td>
                        @if($account->tree_level==1)
                        <td class="pl-4">{{$account->account_no}}</td>
                        @elseif($account->tree_level==2)
                        <td class="pl-5">{{$account->account_no}}</td>
                        @else
                        <td>{{$account->account_no}}</td>
                        @endif
                        <td><a href="{{route('accounts.view', $account->id)}}">{{$account->account_name}}</a></td>
                        <td>{{$account->accountType->name}}</td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-tool" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i></button>
                            <div class="dropdown-menu dropdown-menu-right" role="menu">
                                <a href="{{route('accounts.view', $account->id)}}" class="dropdown-item"><i class="fas fa-search"></i> Detail</a>
                                <a href="{{route('accounts.edit', $account->id)}}" class="dropdown-item"><i class="fas fa-edit"></i> Edit</a>
                                @if($account->isLocked()!=1 && $account->has_children==0)
                                <form method="POST" action="{{route('accounts.delete',$account->id)}}" style="display:inline">
                                    @method('DELETE') @csrf
                                    <button type="button" class="dropdown-item btn-delete"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
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
          html:'Apakah Anda yakin ingin menghapus item tersebut?',
          showCloseButton: false,
          showCancelButton: true,
          focusConfirm: false
        }).then(function(result){
          if(result.value){
            form.submit();
          }
        })
      });

    $('.collapse-btn').on('click', function(e){
        var id = $(this).attr('data-id');
        var iscollapse = $(this).attr('data-collapse');
        console.log(iscollapse)
        if(iscollapse=='true'){
            $(this).html('<i class="fas fa-minus"></i>');
            $(this).attr('data-collapse', 'false');
            $('.tr-row').each(function(e){
                var parent = $(this).attr('data-parent');
                if(parent==id){
                    $(this).show()
                }
            });
        }else{
            $(this).html('<small><i class="fas fa-plus"></i></small>');
            $(this).attr('data-collapse', 'true');
            $('.tr-row').each(function(e){
                var parent = $(this).attr('data-parent');
                if(parent==id){
                    $(this).hide()
                }
            });
        }
    })
})

</script>
@endpush