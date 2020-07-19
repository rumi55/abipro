@php 
$active_menu='accounts'; 
$breadcrumbs = array(
    ['label'=>'Akun', 'url'=>route('accounts.index')],
    ['label'=>'Saldo Awal'],
);
@endphp
@extends('layouts.app')
@section('title', 'Saldo Awal')
@section('content')
<form id="balance-form" action="{{route('accounts.opening_balance.save')}}" method="POST">
@csrf
<div class="card">
    <div class="card-header">    
        <h4 class="card-title">Saldo Awal per {{fdate($balance_date)}}</h4>
    </div>
    <div class="card-body pb-1">    
        <div class="table-responsive mt-4" style="height:400px">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th style="width:10%">Kode</th>
                        <th style="width:40%">Akun</th>
                        <th style="width:25%">Debet</th>
                        <th style="width:25%">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                @php $type = null; @endphp
                @foreach($accounts as $account)
                    @if($type!=$account->account_type_id)
                    @php $type = $account->account_type_id; @endphp
                    <tr>
                        <td class="font-weight-bold" colspan="4">{{$account->accountType->name}}</td>
                    </tr>
                    @endif
                    <tr id="row-{{$account->id}}" class="tr-row" data-id="{{$account->id}}" data-parent="{{$account->account_parent_id}}">
                        @if($account->tree_level==1)
                        <td class="pl-4" style="vertical-align:middle">{{$account->account_no}}</td>
                        @elseif($account->tree_level==2)
                        <td class="pl-5" style="vertical-align:middle">{{$account->account_no}}</td>
                        @else
                        <td style="vertical-align:middle">{{$account->account_no}}</td>
                        @endif
                        <td style="vertical-align:middle">{{$account->account_name}}</td>
                        <td>
                            @if($account->has_children==0)
                            <input style="width:200px" name="debit_{{$account->id}}" data-index="{{$account->id}}" type="text" id="debit_{{$account->id}}" class="form-control debit @error('debit_'.$account->id) is-invalid @enderror" value="{{old('debit_'.$account->id, $account->op_debit)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('debit_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror                                
                            @endif
                        </td>
                        <td>
                            @if($account->has_children==0)
                            <input style="width:200px" name="credit_{{$account->id}}" data-index="{{$account->id}}" type="text" id="credit_{{$account->id}}" class="form-control credit @error('credit_'.$account->id) is-invalid @enderror" value="{{old('credit_'.$account->id, $account->op_credit)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('credit_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror                                
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <table style="width:100%">
            <tfoot>
                <tr>
                    <th colspan="2" style="width:50%">
                        Total
                    </th>
                    <th class="text-right" style="width:25%">
                        <input style="width:200px" tabindex="-1" name="total_debit" type="text" class="total form-control-plaintext text-success text-bold" readonly id="total_debit" value="" data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask/>
                        @error('total_debit')<small class="text-danger">{!! $message !!}</small>@enderror
                    </th>
                    <th class="text-right" style="width:25%">
                        <input style="width:200px" tabindex="-1" name="total_credit" type="text" class="total form-control-plaintext text-success text-bold" readonly id="total_credit" value="" data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask/>
                        @error('total_credit')<small class="text-danger">{!! $message !!}</small>@enderror
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer">
        <a  href="{{route('accounts.index')}}" class="btn btn-default">Batal</a>
        <button type="submit" class="btn btn-primary float-right">Simpan</button>
    </div>
</div>
</form>
@endsection
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
    $(".select2").select2({theme: 'bootstrap4'});
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