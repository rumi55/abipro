@php 
$type = $journal->is_voucher==1?'vouchers':'journals';
$title_type = $journal->is_voucher==1?'Vouchers':'Jurnal';
$active_menu=$type; 
$breadcrumbs = array(
    ['label'=>$title_type, 'url'=>route('dcru.index',$type)],
    ['label'=>'Detail '.$title_type],
);
@endphp
@extends('layouts.app')
@section('title', 'Detil '.$title_type)
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">{{$title_type}} #{{$journal->trans_no}}</h5>
        <div class="card-tools">
        <button type="button" class="btn btn-tool" data-toggle="dropdown">
          <i class="fas fa-th"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right" role="menu">
            <a href="{{route($type.'.report', $journal->id)}}" class="dropdown-item" ><i class="fas fa-print"></i> Cetak</a>
            <a class="dropdown-divider"></a>
            <a href="{{route($type.'.edit', $journal->id)}}" class="dropdown-item" ><i class="fas fa-edit"></i> Edit</a>
            <a href="{{route($type.'.create.duplicate', $journal->id)}}" class="dropdown-item" ><i class="fas fa-copy"></i> Gandakan</a>
            <form action="{{route('dcru.delete', ['name'=>'journals', 'id'=>$journal->id])}}" method="POST">
                @csrf
                @method('DELETE')
            <a href="#" class="dropdown-item btn-delete" ><i class="fas fa-trash"></i> Hapus</a>
            </form>
        </div>
        </div>
    </div>
    <div class="card-body pb-1">    
        <div class="row">    
            <dl class="col-md-4">
                <dt>Nomor</dt>
                <dd>{{$journal->trans_no}}</dd>
            </dl>    
            <dl class="col-md-4">
                <dt>Tanggal</dt>
                <dd>{{fdate($journal->trans_date)}}</dd>
            </dl>    
            <dl class="col-md-4">
                <dt>Keterangan</dt>
                <dd>{{$journal->description}}</dd>
            </dl>    
        </div>
        <div class="table-responsive mt-4">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Akun</th>
                        <th>Keterangan</th>
                        <th>Departemen</th>
                        <th class="text-right">Debet</th>
                        <th class="text-right">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($journal->details as $detail)
                    <tr>
                        <td>{{$detail->account->account_no}}</td>
                        <td>{{$detail->account->account_name}}</td>
                        <td>{{$detail->description}}</td>
                        <td>{{$detail->department!=null?$detail->department->name:'-'}}</td>
                        <td class="text-right">{{fcurrency($detail->debit)}}</td>
                        <td class="text-right">{{fcurrency($detail->credit)}}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4"></th>
                        <th class="text-right">
                            <small class="text-muted">Total Debet</small><br>
                            <span class="text-success">{{fcurrency($journal->total)}}</span>
                        </th>
                        <th class="text-right">
                            <small class="text-muted">Total Kredit</small><br>
                            <span class="text-success">{{fcurrency($journal->total)}}</span>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="row mt-5">
            <div class="col-sm-6">
                @if($journal->createdBy!=null)
                <small>Dibuat oleh <a href="#">{{$journal->createdBy->name}}</a> pada {{fdatetime($journal->created_at)}}</small>
                @endif
            </div>
            <div class="col-sm-6 text-right">
                @if($journal->updatedBy!=null)
                <small>Terakhir diperbarui oleh {{$journal->updatedBy->name}} pada {{fdatetime($journal->updated_at)}}</small>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-sm-6">
            @if(!empty($prev_id))
            <a title="{{$title_type}} Sebelumnya" href="{{route($type.'.view', $prev_id)}}"><i class="fas fa-chevron-left"></i></a>
            @endif
            </div>
            <div class="col-sm-6 text-right">
            @if(!empty($next_id))
            <a title="{{$title_type}} Selanjutnya" href="{{route($type.'.view', $next_id)}}"><i class="fas fa-chevron-right"></i></a>
            @endif
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script type="text/javascript">
$(function(){
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
})
</script>
@endpush