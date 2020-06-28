@php 
$active_menu='accounts'; 
$breadcrumbs = array(
    ['label'=>trans('Account')]
);
@endphp
@extends('layouts.app')
@section('title', trans('Account'))
@section('content-header-right')
@if(has_action('accounts', 'create') && has_action('accounts', 'import'))
<div class="btn-group">
  <a href="{{route('accounts.create')}}" class="btn btn-primary" ><i class="fas fa-plus"></i> {{__('New Account')}}</a>
  <button type="button" class="btn btn-primary dropdown-toggle dropdown-icon" data-toggle="dropdown"></button>
  <span class="sr-only">Toggle Dropdown</span>
  <div class="dropdown-menu" role="menu">
    <a href="{{route('accounts.import')}}" class="dropdown-item" ><i class="fas fa-upload"></i> {{__('Import Accounts')}}</a>
  </div>
</div>
@elseif(has_action('accounts', 'create'))
    <a href="{{route('accounts.create')}}" class="btn btn-primary" ><i class="fas fa-plus"></i> {{__('New Account')}}</a>
@elseif(has_action('accounts', 'import'))
    <a href="{{route('accounts.import')}}" class="btn btn-primary" ><i class="fas fa-upload"></i> {{__('Import Accounts')}}</a>
@endif
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">{{__('Chart of Accounts')}}</h5>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-toggle="dropdown">
            <i class="fas fa-th"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" role="menu">
                <a href="{{route('accounts.opening_balance')}}" class="dropdown-item">{{__('Opening Balance')}}</a>
                <a href="{{route('accounts.budgets')}}" class="dropdown-item">{{__('Budget')}}</a>
            </div>
        </div>
    </div>
    <div class="card-body pb-1">    
        <div class="table-responsive mt-4">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th style="width:20px">
                        </th>
                        <th>{{__('Account No.')}}</th>
                        <th>{{__('Account Name')}}</th>
                        <th>{{__('Type')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @if(count($accounts)==0)
                    <tr><td class="text-center" colspan="5">
                    {{__('No account available.')}}
                    <div class="mt-5">
                    <a href="{{route('accounts.create')}}" class="btn btn-primary" ><i class="fas fa-plus"></i> {{__('New Account')}}</a>
                    <a href="{{route('accounts.import')}}" class="btn btn-primary" ><i class="fas fa-upload"></i> {{__('Import Account')}}</a>
                    </div>
                    </td></tr>
                @endif
                @foreach($accounts as $account)
                    <tr id="row-{{$account->id}}" class="tr-row" data-id="{{$account->id}}" data-tree-level="{{$account->tree_level}}" data-has-children="{{$account->has_children}}" data-parent="{{$account->account_parent_id}}">
                        <td>
                            @if($account->has_children)
                            <a class="collapse-btn" href="javascript:void(0)" data-id="{{$account->id}}" data-collapse="false">
                                <i class="fas fa-angle-down fa-xs"></i>
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
                        <td><a href="{{route('accounts.view', $account->id)}}">{{tt($account,'account_name')}}</a></td>
                        <td>{{tt($account->accountType,'name')}}</td>
                        <td>
                            <button type="button" class="btn btn-tool" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i></button>
                            <div class="dropdown-menu dropdown-menu-right" role="menu">
                                <a href="{{route('accounts.view', $account->id)}}" class="dropdown-item"><i class="fas fa-search"></i> {{__('Detail')}}</a>
                                <a href="{{route('accounts.edit', $account->id)}}" class="dropdown-item"><i class="fas fa-edit"></i> {{__('Edit')}}</a>
                                @if($account->isLocked()!=1 && $account->has_children==0)
                                <form method="POST" action="{{route('accounts.delete',$account->id)}}" style="display:inline">
                                    @method('DELETE') @csrf
                                    <button type="button" class="dropdown-item btn-delete"><i class="fas fa-trash"></i> {{__('Delete')}}</button>
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
          title: '{{__("Warning")}}!',
          icon: 'warning',
          html:'{{__("Are you sure want to delete the item?")}}',
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
        if(iscollapse=='true'){
            collapse(false, id)
        }else{
            collapse(true, id)
        }
    })
    collapseAll(true);
})
function collapse(hide, parent_id){
    $('.tr-row[data-parent='+parent_id+']').each(function(e){
        $('a[data-id='+parent_id+']').html('<i class="fas fa-angle-'+(hide?'right':'down')+' fa-xs"></i>');
        $('a[data-id='+parent_id+']').attr('data-collapse', hide);
        var id = $(this).attr('data-id');
        if(hide){
            collapse(hide, id);
            $(this).hide()
        }else{
            $(this).show()
        }
    });
}
function collapseAll(collapse){
    $('.tr-row').each(function(e){
        var id = $(this).attr('data-id');
        var level = $(this).attr('data-tree-level');
        var child = $(this).attr('data-has-children');
        if(child=='1'){
            $('a[data-id='+id+']').html('<i class="fas fa-angle-'+(collapse?'right':'down')+' fa-xs"></i>');
            $('a[data-id='+id+']').attr('data-collapse', collapse);
        }
        if(level>0){
            if(collapse){
                $(this).hide();
            }else{
                $(this).show();
            }
        }
    });
    $('#collapseall-btn').html('<i class="fas fa-angle-'+(collapse?'right':'down')+' fa-xs"></i>');
    $('#collapseall-btn').attr('data-collapse', collapse);
}
</script>
@endpush