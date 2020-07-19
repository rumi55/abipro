@php 
$active_menu='accounts'; 
$breadcrumbs = array(
    ['label'=>'Akun', 'url'=>route('accounts.index')],
    ['label'=>'Anggaran'],
);
@endphp
@extends('layouts.app')
@section('title', 'Anggaran')
@section('content')

<div class="card">
    <div class="card-header">
        <form action="{{route('accounts.budgets')}}" method="get">
            <div class="row form-group">
            <label class="col-sm-1 col-form-label">Bulan</label>
            <div class="col-md-2">
                <select class="form-control select2" name="start_month">
                    @foreach($month_list as $m)
                    <option {{request('start_month', $start_month)==$m['value']?'selected':''}} value="{{$m['value']}}">{{$m['text']}}</option>
                    @endforeach
                </select>
            </div>
            <label class="col-sm-1 col-form-label">Sampai</label>
            <div class="col-md-2">
                <select class="form-control select2" name="end_month">
                    @foreach($month_list as $m)
                    <option {{request('end_month', $end_month)==$m['value']?'selected':''}} value="{{$m['value']}}">{{$m['text']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
            </form>
        </div>
    </div>
    <form id="budget-form" action="{{route('accounts.budgets.save')}}" method="POST">
@csrf
    <div class="card-body pb-1">    
        <div class="table-responsive mt-4" style="height:400px">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Akun</th>
                        @foreach($months as $month)
                        <th>{{$month}}</th>
                        @endforeach
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
                        @foreach($columns as $column)
                        <td>
                            @if($account->has_children==0)
                            <input style="width:100px" name="bdg_{{$account->id}}_{{$column}}" value="{{array_key_exists($account->id.'_'.$column, $budgets)?$budgets[$account->id.'_'.$column]:''}}"  data-index="{{$account->id}}_{{$column}}" type="text" id="bdg_{{$account->id}}_{{$column}}" class="form-control debit @error('debit_'.$account->id) is-invalid @enderror" data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('debit_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror                                
                            @endif
                        </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </form>
    <div class="card-footer">
        <a  href="{{route('accounts.index')}}" class="btn btn-default">Batal</a>
        <button id="btn-submit" type="submit" class="btn btn-primary float-right">Simpan</button>
    </div>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
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
    $('[data-mask]').inputmask();
    $(".select2").select2({theme: 'bootstrap4'});
    $("#btn-submit").click(function(e){
        $('#budget-form').submit() 
    });
})
</script>

@endpush