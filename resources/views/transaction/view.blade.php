@php
$active_menu='vouchers';
$breadcrumbs = array(
    ['label'=>trans('Vouchers'), 'url'=>route('dcru.index','vouchers')],
    ['label'=>trans('Voucher Detail')],
);
@endphp
@extends('layouts.app')
@section('title', trans('Voucher Detail'))
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">
        {{__('Voucher')}} #{{$transaction->trans_no}}
        @php $status =['draft'=>'secondary', 'submitted'=>'warning', 'approved'=>'success', 'rejected'=>'danger']; @endphp
        <span class="badge badge-{{$status[$transaction->status]}}">{{$transaction->status}}</span>
        </h5>
        <div class="card-tools">
        <button type="button" class="btn btn-tool" data-toggle="dropdown">
          <i class="fas fa-th"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right" role="menu">
            @if(has_action('vouchers', 'print') && $transaction->status=='approved')
            <a href="{{route('vouchers.receipt', $transaction->journal->id)}}" class="dropdown-item" ><i class="fas fa-print"></i> {{__('Print Receipt')}}</a>
            <a href="{{route('vouchers.voucher', $transaction->journal->id)}}" class="dropdown-item" ><i class="fas fa-print"></i> {{__('Print Voucher')}}</a>
            @endif
            @if(has_action('vouchers', 'tojournal') && $transaction->status=='approved')
            <form action="{{route('vouchers.tojournal', ['id'=>$transaction->journal->id])}}" method="POST">
            @csrf
            <button class="dropdown-item" ><i class="fas fa-exchange-alt"></i> {{__('Process to Journal')}}</button>
            </form>
            @endif
            @if(has_action('vouchers', 'edit') && ($transaction->status=='draft' || $transaction->status=='rejected'))
            <a href="{{route('vouchers.edit.single', $transaction->id)}}" class="dropdown-item" ><i class="fas fa-edit"></i> {{__('Edit')}}</a>
            @endif
            @if(has_action('vouchers', 'create'))
            <a href="{{route('vouchers.create.single.duplicate', $transaction->id)}}" class="dropdown-item" ><i class="fas fa-copy"></i> {{__('Duplicate')}}</a>
            @endif
            @if(has_action('vouchers', 'delete') && ($transaction->status=='draft' || $transaction->status=='rejected'))
            <form action="{{route('vouchers.delete', ['id'=>$transaction->journal->id])}}" method="POST">
                @csrf
                @method('DELETE')
            <a href="#" class="dropdown-item btn-delete text-danger" ><i class="fas fa-trash"></i> {{__('Delete')}}</a>
            </form>
            @endif
        </div>
        </div>
    </div>
    <div class="card-body pb-1">
        <div class="row">
            <dl class="col-md-4">
                <dt>{{__('Transaction No.')}}</dt>
                <dd>{{$transaction->trans_no}}</dd>
            </dl>
            <dl class="col-md-4">
                <dt>{{__('Transaction Date')}}</dt>
                <dd>{{fdate($transaction->trans_date)}}</dd>
            </dl>
            <dl class="col-md-4">
                <dt>{{$transaction->trans_type=='in'?__('Payer'):__('Beneficiary')}}</dt>
                <dd><a href="{{route('contacts.view',$transaction->contact_id)}}">{{$transaction->contact!=null?$transaction->contact->name:'-'}}</a></dd>
            </dl>
            <dl class="col-md-4">
                <dt>{{__('Department')}}</dt>
                <dd>{{$transaction->department!=null?$transaction->department->name:'-'}}</dd>
            </dl>
            <dl class="col-md-4">
                <dt>{{__('Account')}}</dt>
                <dd><a href="{{route('accounts.view',$transaction->account_id)}}">{{$transaction->account!=null?'('.$transaction->account->account_no.') '.$transaction->account->account_name:'-'}}</a></dd>
            </dl>
            <dl class="col-md-4">
                <dt>{{__('Description')}}</dt>
                <dd>{{$transaction->description??'-'}}</dd>
            </dl>
        </div>
        <div class="table-responsive mt-4">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{__('Account')}}</th>
                        <th>{{__('Description')}}</th>
                        <th>{{__('Department')}}</th>
                        <th class="text-right">{{__('Amount')}}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($transaction->details as $detail)
                    <tr>
                        <td><a href="{{route('accounts.view',$detail->account_id)}}">({{$detail->account->account_no}}) {{$detail->account->account_name}}</a></td>
                        <td>{{$detail->description}}
                            @if(!empty($detail->tags))
                            <p>
                                @foreach($detail->getTags() as $tag)
                                    <span class="badge badge-secondary">{{$tag->item_name}}</span>
                                @endforeach
                            </p>
                            @endif
                        </td>
                        <td>
                            {{$detail->department!=null?$detail->department->name:'-'}}

                        </td>
                        <td class="text-right">{{fcurrency($detail->amount)}}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3"></th>
                        <th class="text-right">
                            <small class="text-muted">{{__('Total')}}</small><br>
                            <span class="text-success">{{fcurrency($transaction->amount)}}</span>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="row mt-5">
            <div class="col-sm-4">
                @if($transaction->createdBy!=null)
                <small><b>{{__('Created by')}}</b> <a href="{{route('users.view',$transaction->created_by)}}">{{$transaction->createdBy->name}}</a> {{__('at')}} {{fdatetime($transaction->created_at)}}</small>
                @endif
            </div>
            <div class="col-sm-4 text-center">
                @if($transaction->status=='rejected' && !empty($transaction->approved_by))
                <small><b>{{__('Rejected by')}}</b> <a href="{{route('users.view',$transaction->approved_by)}}">{{$transaction->approvedBy->name}}</a> {{__('at')}} {{fdatetime($transaction->approved_at)}}</small><br>
                <small><b>{{__('Rejection Note')}}:</b> {{$transaction->rejection_note}}</small>
                @endif
                @if($transaction->status=='approved' && !empty($transaction->approved_by))
                <small><b>{{__('Approved by')}}</b> <a href="{{route('users.view',$transaction->approved_by)}}">{{$transaction->approvedBy->name}}</a> {{__('at')}} {{fdatetime($transaction->updated_at)}}</small>
                @endif
            </div>
            <div class="col-sm-4 text-right">
                @if($transaction->updatedBy!=null)
                <small><b>{{__('Last updated by')}}</b> <a href="{{route('users.view',$transaction->updated_by)}}">{{$transaction->updatedBy->name}}</a> {{__('at')}} {{fdatetime($transaction->updated_at)}}</small>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-sm-2">
            @if(!empty($prev_id))
            <a title="{{__('Previous')}}" href="{{route('vouchers.view', $prev_id)}}"><i class="fas fa-chevron-left"></i></a>
            @endif
            </div>
            <div class="col-sm-8 text-center">
            @if($transaction->status=='submitted' && has_action('vouchers', 'approve'))
            <form action="{{route('vouchers.approve.transaction', $transaction->id)}}" method="POST">
            @csrf
            <input id="rejection-note" type="hidden" name="rejection_note" />
            <input id="status" type="hidden" name="status" />
            <button type="submit" class="btn btn-success btn-approve">{{__('Approve')}}</button>
            <button type="submit" class="btn btn-danger btn-reject">{{__('Reject')}}</button>
            </form>
            @endif
            @if($transaction->status=='draft' && has_action('vouchers', 'create') && user('id')==$transaction->created_by)
            <form action="{{route('vouchers.create.submit.transaction', $transaction->id)}}" method="POST">
            @csrf
            <input id="status" type="hidden" name="status" value="submitted" />
            <button type="submit" class="btn btn-success">{{__('Submit')}}</button>
            </form>
            @endif
            </div>
            <div class="col-sm-2 text-right">
            @if(!empty($next_id))
            <a title="{{__('Next')}}" href="{{route('vouchers.view', $next_id)}}"><i class="fas fa-chevron-right"></i></a>
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
