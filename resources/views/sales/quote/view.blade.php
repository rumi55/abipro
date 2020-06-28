@php 
$active_menu="sales_quotes";
$page_title = trans('Sales Quote');
$breadcrumbs = array(
    ['label'=>trans('Sales'), 'url'=>route('dcru.index','sales_invoices')],
    ['label'=>trans('Sales Quote'), 'url'=>route('dcru.index','sales_quotes')],
    ['label'=>$page_title],
);
@endphp
@extends('layouts.app')
@section('title', $page_title)
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">{{__('Sales Quote')}} #{{$transaction->trans_no}}</h5>
        <div class="card-tools">
        <button type="button" class="btn btn-tool" data-toggle="dropdown">
          <i class="fas fa-th"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right" role="menu">
            <a href="{{route('sales_invoices.create.quotes', $transaction->id)}}" class="dropdown-item" ><i class="fas fa-money-check"></i> {{__('Create Invoice')}}</a>
            <a href="{{route('sales_orders.create.quotes', $transaction->id)}}" class="dropdown-item" ><i class="fas fa-shopping-cart"></i> {{__('Create Order')}}</a>
            <a href="{{route('sales_quotes.edit', $transaction->id)}}" class="dropdown-item" ><i class="fas fa-edit"></i> {{__('Edit')}}</a>
            <a href="{{route('sales_quotes.create.duplicate', $transaction->id)}}" class="dropdown-item" ><i class="fas fa-copy"></i> {{__('Duplicate')}}</a>
            <form action="{{route('sales_quotes.delete', ['id'=>$transaction->id])}}" method="POST">
                @csrf
                @method('DELETE')
            <a href="#" class="dropdown-item btn-delete" ><i class="fas fa-trash"></i> {{__('Delete')}}</a>
            </form>
        </div>
        </div>
    </div>
    <div class="card-body pb-1">    
        <div class="row">    
            <dl class="col-md-4">
                <dt>{{__('Customer')}}</dt>
                <dd>{{$transaction->customer->name}}</dd>
            </dl>    
            <dl class="col-md-4">
                <dt>{{__('Transaction No.')}}</dt>
                <dd>{{$transaction->trans_no}}</dd>
            </dl>    
            <dl class="col-md-4">
                <dt>{{__('Transaction Date')}}</dt>
                <dd>{{fdate($transaction->trans_date)}}</dd>
            </dl>    
            <dl class="col-md-4">
                <dt>{{__('Description')}}</dt>
                <dd>{{$transaction->description}}</dd>
            </dl>    
        </div>
        <div class="table-responsive mt-4">
            <table class="table table-hover">
                <thead class="thlight">
                    <tr>
                        <th>{{__('Product')}}</th>
                        <th>{{__('Description')}}</th>
                        <th>{{__('Unit')}}</th>
                        <th class="text-right">{{__('Unit Price')}}</th>
                        <th class="text-right">{{__('Discount')}}</th>
                        <th>{{__('Tax')}}</th>
                        <th class="text-right">{{__('Amount')}}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($transaction->details as $detail)
                    <tr>
                        <td>{{$detail->product->name}}</td>
                        <td>{{$detail->description}}</td>
                        <td>{{$detail->unit->name}}</td>
                        <td class="text-right">{{fcurrency($detail->unit_price)}}</td>
                        <td class="text-right">{{fcurrency($detail->discount)}}</td>
                        <td>{{$detail->taxes==null?'-':$detail->taxes->name}}</td>
                        <td class="text-right">{{fcurrency($detail->amount)}}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-right">{{__('Subtotal')}}</th>
                        <th class="text-right font-weight-normal">{{fcurrency($transaction->subtotal)}}</th>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-right font-weight-bold">{{__('Tax')}}</td>
                        <td class="text-right">{{fcurrency($transaction->tax)}}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-right font-weight-bold">{{__('Total')}}</td>
                        <td class="text-right">{{fcurrency($transaction->total)}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="row mt-5">
            <div class="col-sm-6">
                @if($transaction->createdBy!=null)
                <small>Dibuat oleh <a href="#">{{$transaction->createdBy->name}}</a> pada {{fdatetime($transaction->created_at)}}</small>
                @endif
            </div>
            <div class="col-sm-6 text-right">
                @if($transaction->updatedBy!=null)
                <small>Terakhir diperbarui oleh {{$transaction->updatedBy->name}} pada {{fdatetime($transaction->updated_at)}}</small>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-sm-6">
            @if(!empty($prev_id))
            <a title="{{__('Previouse')}}" href="{{route('sales_quotes.view', $prev_id)}}"><i class="fas fa-chevron-left"></i></a>
            @endif
            </div>
            <div class="col-sm-6 text-right">
            @if(!empty($next_id))
            <a title="{{__('Next')}}" href="{{route('sales_quotes.view', $next_id)}}"><i class="fas fa-chevron-right"></i></a>
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