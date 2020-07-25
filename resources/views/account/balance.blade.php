@php
$active_menu='accounts';
$breadcrumbs = array(
    ['label'=>trans('Chart of Account'), 'url'=>route('accounts.index')],
    ['label'=>trans('Opening Balance')],
);
@endphp
@extends('layouts.app')
@section('title', trans('Opening Balance'))
@section('content')

<div class="card">
    <div class="card-body">
    <form action="{{route('accounts.opening_balance')}}" method="get">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="account_type" >{{__('Account Type')}}</label>
                    <select id="account_type" name="account_type_id" class="form-control select2"></select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="department_id" >{{__('Department')}}</label>
                    <select id="department_id" name="department_id" class="form-control select2"></select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <button type="submit" class="btn btn-info mt-4">Filter</button>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>
<form id="balance-form" action="{{route('accounts.opening_balance.save')}}" method="POST">
@csrf
<div class="card">
    <div class="card-body">
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th style="width:20%">{{__('Account No.')}}</th>
                    <th style="width:60%">{{__('Account Name')}}</th>
                    <th style="width:20%" class="text-right">{{__('Opening Balance')}}</th>
                </tr>
            </thead>
        </table>

        <div class="table-responsive" style="height:400px">
            <table class="table table-hover table-sm">
                <tbody>
                @php $type = null; @endphp
                @foreach($accounts as $account)
                    @if($type!=$account->account_type_id)
                    @php $type = $account->account_type_id; @endphp
                    <tr>
                        <td class="font-weight-bold" colspan="4">{{$account->account_type_name}}</td>
                    </tr>
                    @endif
                    <tr id="row-{{$account->id}}" class="tr-row" data-id="{{$account->id}}" data-parent="{{$account->account_parent_id}}">
                        @if($account->tree_level==1)
                        <td class="pl-4" style="vertical-align:middle;width:20%">{{$account->account_no}}</td>
                        @elseif($account->tree_level==2)
                        <td class="pl-5" style="vertical-align:middle;width:20%">{{$account->account_no}}</td>
                        @else
                        <td style="vertical-align:middle;width:20%">{{$account->account_no}}</td>
                        @endif
                        <td style="vertical-align:middle;width:60%">{{$account->account_name}}</td>
                        <td class="text-right" style="width:20%">
                            @if($account->has_children==0)
                            <input style="width:200px" name="balance[{{$account->id}}]" data-index="{{$account->id}}" type="text" id="balance_{{$account->id}}" class="form-control" value="{{empty(old('balance.'.$account->id, $account->balance))?'0':old('balance.'.$account->id, $account->balance)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror

                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if(!empty(request('department_id')))
                <input type="hidden" name="department_id" value="{{request('department_id')}}" />
            @endif
            @if(!empty(request('account_type_id')))
                <input type="hidden" name="account_type_id" value="{{request('account_type_id')}}" />
            @endif
        </div>
    </div>
    <div class="card-footer">
        <a  href="{{route('accounts.index')}}" class="btn btn-default">Batal</a>
        <button type="submit" class="btn btn-primary float-right">Simpan</button>
    </div>
</div>
</form>
@endsection
@push('css')
  <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush
@push('js')
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/inputmask/min/jquery.inputmask.bundle.min.js')}}"></script>
<script type="text/javascript">
$(function(){
    $('.debit').change(function(){
        var idx = $(this).attr('data-index');
        var val = parseNumber($(this).val());
        if(val>0){
            $('#credit_'+idx).val(0);
        }
        onchange()
    })
    $('.credit').change(function(){
        var idx = $(this).attr('data-index');
        var val = parseNumber($(this).val());
        if(val>0){
            $('#debit_'+idx).val(0);
        }
        onchange()
    })

    $('.date').daterangepicker({
      timePicker: false,
      singleDatePicker:true,
      autoApply:true,
      locale: {
        format: 'DD-MM-YYYY'
      }
    });
    $('.currency').inputmask({ 'alias': 'currency' })
    $('[data-mask]').inputmask();
    $.ajax({
        url: BASE_URL+'/json/account_types',
        dataType: 'json',
        cache: false,
        success:function(res){
            var items =$.map(res, function (item) {
                    return {
                        text: item.name,
                        id: item.id
                    }
                })
            $("#account_type").select2({
                theme: 'bootstrap4',
                allowClear:true,
                placeholder: '{{__("Select Account Type")}}',
                data:items
            })
            $('#account_type').val('{{request("account_type_id")}}')
            $('#account_type').trigger('change')
        }
    })
    $.ajax({
        url: BASE_URL+'/json/departments',
        dataType: 'json',
        cache: false,
        success:function(res){
            var items =$.map(res, function (item) {
                    return {
                        text: item.name,
                        id: item.id
                    }
                })
            $("#department_id").select2({
                theme: 'bootstrap4',
                allowClear:true,
                placeholder: '{{__("Select Department")}}',
                data:items
            })
            $('#department_id').val('{{request("department_id")}}')
            $('#department_id').trigger('change')
        }
    })

    onchange();
    // $('#balance-form').submit(function(e){
    //     e.preventDefault();
    //     var d = parseNumber($('#total_debit').val());
    //     var c = parseNumber($('#total_credit').val());
    //     if(d!=c){
    //         Swal.fire({
    //         title: 'Peringatan!',
    //         icon: 'warning',
    //         html:'Total debet dan kredit tidak sama. Jika Anda klik tombol OK maka selisihnya akan disimpan ke akun ekuitas saldo awal. Untuk memperbaiki klik tombol cancel.',
    //         showCloseButton: false,
    //         showCancelButton: true,
    //         focusConfirm: false
    //         }).then(function(result){
    //             if(result.value){
    //                 form.submit();
    //             }
    //         })
    //     }
    //     $(this).submit();
    // });
})
function onchange(){
    var debit = sum('.debit');
    var credit = sum('.credit');
    $('#total_debit').val(debit);
    $('#total_credit').val(credit);
    if(debit==credit){
        $('.total').addClass('text-success');
        $('.total').removeClass('text-danger');
        $('#total').val(debit);
        $('#btn-save').prop('disabled', false);
    }else{
        $('.total').addClass('text-danger');
        $('.total').removeClass('text-success');
        $('#btn-save').prop('disabled', true);
    }
}
function sum(selector){
    var total = 0;
   $(selector).each(function(index){
      var val = $(this).val();
      if(val==''||val==null){
        val = 0;
        $(this).val(val)
      }
      var n = parseNumber(val);
      total=total+n;
    });
    return total;
}
function parseNumber(val){
    if(val=='' || val==null)return 0;
    return parseInt(val.split('.').join(''));
}
</script>

@endpush
