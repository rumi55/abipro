@php
$type = $journal->is_voucher==1?'vouchers':'journals';
$title_type = $journal->is_voucher==1?'Voucher':'Jurnal';
$active_menu=$type;
$breadcrumbs = array(
    ['label'=>$title_type, 'url'=>route('dcru.index',$type)],
    ['label'=>'Detail '.$title_type],
);
@endphp
@extends('layouts.app')
@section('title', 'Detail '.$title_type)
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">{{$title_type}} #{{$journal->trans_no}}
            @if($journal->is_voucher==1 && $journal->is_processed==0)
            @php $status =['draft'=>'secondary', 'submitted'=>'warning', 'approved'=>'success', 'rejected'=>'danger']; @endphp
            <span class="badge badge-{{$status[$journal->status]}}">{{$journal->status}}</span>
            @endif
        </h5>
        <div class="card-tools">
        @if(has_action($type, 'report') || (has_action($type, 'edit') && !$journal->is_locked)
        || has_action($type, 'create')|| (has_action($type, 'delete') && !$journal->is_locked)
        )
        <button type="button" class="btn btn-tool" data-toggle="dropdown">
          <i class="fas fa-th"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right" role="menu">


            @if($journal->is_voucher && has_action($type, 'report') && $journal->status=='approved'  && $journal->is_processed==false)
            <a href="{{route($type.'.receipt', $journal->id)}}" class="dropdown-item" ><i class="fas fa-print"></i> Cetak Kuitansi</a>
            @endif
            @if(has_action($type, 'edit') && !$journal->is_locked)
            <a href="{{route($type.'.edit', $journal->id)}}" class="dropdown-item" ><i class="fas fa-edit"></i> Edit</a>
            @endif
            @if(has_action($type, 'create'))
            <a href="{{route($type.'.create.duplicate', $journal->id)}}" class="dropdown-item" ><i class="fas fa-copy"></i> Gandakan</a>
            @endif
            @if(has_action($type, 'delete') && !$journal->is_locked)
            <form action="{{route('dcru.delete', ['name'=>'journals', 'id'=>$journal->id])}}" method="POST">
                @csrf
                @method('DELETE')
            <a href="#" class="dropdown-item btn-delete" ><i class="fas fa-trash"></i> Hapus</a>
            </form>
            @endif
        </div>
            @endif
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
            @if($journal->is_voucher==1 && $journal->is_processed==0 && !empty($journal->contact_id))
            <dl class="col-md-4">
                <dt>{{__('Contact')}}</dt>
                <dd><a href="{{route('contacts.view',$journal->contact_id)}}">{{$journal->contact!=null?$journal->contact->name:'-'}}</a></dd>
            </dl>
            @endif
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
                        <td><a href="{{route('accounts.view', $detail->account_id)}}">{{$detail->account->account_no}}</a></td>
                        <td><a href="{{route('accounts.view', $detail->account_id)}}">{{$detail->account->account_name}}</a></td>
                        <td>{{$detail->description}}
                            @if(!empty($detail->tags))
                            <p>
                                @foreach($detail->getTags() as $tag)
                                    <span class="badge badge-secondary">{{$tag->item_name}}</span>
                                @endforeach
                            </p>
                            @endif
                        </td>
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
            <div class="col-sm-4">
                @if($journal->createdBy!=null)
                <small>Dibuat oleh <a href="{{route('users.view', $journal->created_by)}}">{{$journal->createdBy->name}}</a> pada {{fdatetime($journal->created_at)}}</small>
                @endif
            </div>
            <div class="col-sm-4 text-center">
                @if($journal->is_voucher && $journal->is_processed==0)
                @if($journal->status=='rejected' && !empty($journal->approved_by))
                <small><b>{{__('Rejected by')}}</b> <a href="{{route('users.view',$journal->approved_by)}}">{{$journal->approvedBy->name}}</a> {{__('at')}} {{fdatetime($journal->approved_at)}}</small><br>
                <small><b>{{__('Rejection Note')}}:</b> {{$journal->rejection_note}}</small>
                @endif
                @if($journal->status=='approved' && !empty($journal->approved_by))
                <small><b>{{__('Approved by')}}</b> <a href="{{route('users.view',$journal->approved_by)}}">{{$journal->approvedBy->name}}</a> {{__('at')}} {{fdatetime($journal->updated_at)}}</small>
                @endif
                @endif
            </div>
            <div class="col-sm-4 text-right">
                @if($journal->updatedBy!=null)
                <small>Terakhir diperbarui oleh <a href="{{route('users.view', $journal->updated_by)}}"> {{$journal->updatedBy->name}}</a> pada {{fdatetime($journal->updated_at)}}</small>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-sm-2">
            @if(!empty($prev_id))
            <a title="{{$title_type}} Sebelumnya" href="{{route($type.'.view', $prev_id)}}"><i class="fas fa-chevron-left"></i></a>
            @endif
            </div>
            <div class="col-sm-8 text-center">
                @if($journal->is_voucher && $journal->is_processed==0)
                @if($journal->status=='submitted' && has_action('vouchers', 'approve'))
                <form action="{{route('vouchers.approve', $journal->id)}}" method="POST">
                @csrf
                <input id="rejection-note" type="hidden" name="rejection_note" />
                <input id="status" type="hidden" name="status" />
                <button type="submit" class="btn btn-success btn-approve">{{__('Approve')}}</button>
                <button type="submit" class="btn btn-danger btn-reject">{{__('Reject')}}</button>
                </form>
                @endif
                @if($journal->status=='draft' && has_action('vouchers', 'create') && user('id')==$journal->created_by)
                <form action="{{route('vouchers.create.submit', $journal->id)}}" method="POST">
                @csrf
                <input id="status" type="hidden" name="status" value="submitted" />
                <button type="submit" class="btn btn-success">{{__('Submit')}}</button>
                </form>
                @endif
                @endif
            </div>
            <div class="col-sm-2 text-right">
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
      $('.btn-approve').click(function(e){
        e.preventDefault();
        var form = $(this).parent();
        Swal.fire({
          title: '{{__("Warning")}}',
          icon: 'warning',
          html:'{{__("Are you sure want to approve this voucher?")}}',
          showCloseButton: false,
          showCancelButton: true,
          focusConfirm: false
        }).then(function(result){
          if(result.value){
              $('#status').val('approved');
            form.submit();
          }
        })
      });
    $('.btn-reject').click(function(e){
        e.preventDefault();
        var form = $(this).parent();
        Swal.fire({
            title: '{{__("Warning")}}',
          icon: 'warning',
          html:'{{__("Are you sure want to reject this voucher?")}} {{__("Write your rejection note below!")}}',
          input: 'textarea',
          showCloseButton: false,
          showCancelButton: true,
          focusConfirm: false
        }).then(function(result){
          if(result.value){
              $('#status').val('rejected');
              $('#rejection-note').val(result.value);
            form.submit();
          }
        })
      });
})
</script>
@endpush
