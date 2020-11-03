@php
$active_menu='accounts';
$breadcrumbs = array(
    ['label'=>__('Accounts'), 'url'=>route('accounts.index')],
    ['label'=>__('Budget')],
);
@endphp
@extends('layouts.app')
@section('title', __('Budget'))
@section('content')

@php $accountType =[];$dept=''; @endphp
<div class="card">
    <div class="card-body">
    <form action="{{route('accounts.budgets')}}" method="get">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="account_type" >{{__('Account Type')}}</label>
                    <select id="account_type" name="account_type_id[]" class="form-control select2" multiple>
                        @foreach($account_types as $type)
                        <option value="{{$type->id}}" {{request('account_type_id')!=null && in_array($type->id, request('account_type_id'))?'selected':''}}>{{tt($type, 'name')}}</option>
                        @php
                            if(request('account_type_id')!=null && in_array($type->id, request('account_type_id'))){
                                $accountType[]=tt($type, 'name');
                            }
                        @endphp
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label>Akun</label>
                <select id="filter_account" name="account[]" class="form-control select2" multiple>
                    @foreach($paccounts as $account)
                    @empty(request('account_type_id'))
                        @if($account->tree_level==0)
                            <option value="{{$account->id}}" {{request('account')!=null && in_array($account->id, request('account'))?'selected':''}} >({{$account->account_no}}) {{$account->account_name}}</option>
                        @endif
                    @else
                        @if($account->tree_level==0 && in_array($account->account_type_id, request('account_type_id')))
                            <option value="{{$account->id}}" {{request('account')!=null && in_array($account->id, request('account'))?'selected':''}} >({{$account->account_no}}) {{$account->account_name}}</option>
                        @endif
                    @endempty
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label>Subakun</label>
                <select id="filter_subaccount" name="subaccount[]" class="form-control select2" multiple>
                    @foreach($paccounts as $account)
                        @empty(request('account'))
                            @empty(request('account_type_id'))
                                @if($account->tree_level==1)
                                    <option value="{{$account->id}}"  {{request('subaccount')!=null && in_array($account->id, request('subaccount'))?'selected':''}}>({{$account->account_no}}) {{$account->account_name}}</option>
                                @endif
                            @else
                                @if($account->tree_level==1 && in_array($account->account_type_id, request('account_type_id')))
                                    <option value="{{$account->id}}"  {{request('subaccount')!=null && in_array($account->id, request('subaccount'))?'selected':''}}>({{$account->account_no}}) {{$account->account_name}}</option>
                                @endif
                            @endempty
                        @else
                            @if($account->tree_level==1 && in_array($account->account_parent_id, request('account')))
                                <option value="{{$account->id}}"  {{request('subaccount')!=null && in_array($account->id, request('subaccount'))?'selected':''}}>({{$account->account_no}}) {{$account->account_name}}</option>
                            @endif
                        @endempty
                    @endforeach
                </select>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="department_id" >{{__('Department')}}</label>
                    <select id="department_id" name="department_id" class="form-control select2"></select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="budget_year" >{{__('Year')}}</label>
                    <select id="budget_year" name="budget_year" class="form-control select2">
                        @for($year=date('Y');$year>=2018;$year--)
                            <option value="{{$year}}" {{ $budget_year == $year ? 'selected':'' }}>{{$year}}</option>
                        @endfor
                    </select>
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
<form id="balance-form" action="{{route('accounts.budgets.save')}}" method="POST">
@csrf
<div class="card">
    <div class="card-header">
    <h3 class="card-title">{{__('Budget')}} {{$budget_year}}</h3>
    </div>
    <div class="card-body">
        <div class="btn-group mb-4">
            <button id="dt-btn-print" type="button" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> {{__('Print')}}</button>
            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
                <span class="sr-only">Toggle Dropdown</span>
                <div class="dropdown-menu" role="menu">
                <a id="dt-btn-pdf" class="dropdown-item" href="#"><i class="fas fa-file-pdf"></i> PDF</a>
                <a id="dt-btn-excel" class="dropdown-item" href="#"><i class="fas fa-file-excel"></i> Excel</a>
                <a id="dt-btn-csv" class="dropdown-item" href="#"><i class="fas fa-file-csv"></i> CSV</a>
                </div>
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th>{{__('Account No.')}}</th>
                        <th>{{__('Account Name')}}</th>
                        <th class="text-center">Jan</th>
                        <th class="text-center">Feb</th>
                        <th class="text-center">Mar</th>
                        <th class="text-center">Apr</th>
                        <th class="text-center">May</th>
                        <th class="text-center">Jun</th>
                        <th class="text-center">Jul</th>
                        <th class="text-center">Aug</th>
                        <th class="text-center">Sep</th>
                        <th class="text-center">Oct</th>
                        <th class="text-center">Nov</th>
                        <th class="text-center">Dec</th>
                        <th class="text-center">{{request('budget_year', date('Y'))}}</th>
                    </tr>
                </thead>
                <tbody id="tdata">
                @php $type = null; @endphp
                @foreach($accounts as $account)
                    @if($type!=$account->account_type_id)
                    @php $type = $account->account_type_id; @endphp

                    @endif
                    <tr id="row-{{$account->id}}" class="tr-row" data-tree-level="{{$account->tree_level}}" data-id="{{$account->id}}" data-parent="{{$account->account_parent_id}}">
                        @if($account->tree_level==1)
                        <td class="pl-4" style="vertical-align:middle;">{{$account->account_no}}</td>
                        @elseif($account->tree_level==2)
                        <td class="pl-5" style="vertical-align:middle;">{{$account->account_no}}</td>
                        @else
                        <td style="vertical-align:middle;">{{$account->account_no}}</td>
                        @endif
                        <td style="vertical-align:middle;">{{$account->account_name}}</td>
                        <td class="text-right">
                            @if($account->has_children==0)
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][jan]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}"  type="text" id="balance_{{$account->id}}_jan" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{fcurrency(empty(old('balance.'.$account->id.'.jan', $account->jan))?'0':old('balance.'.$account->id.'.jan', $account->jan))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror
                            @endif
                        </td>
                        <td class="text-right">
                            @if($account->has_children==0)
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][feb]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_feb" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{fcurrency(empty(old('balance.'.$account->id.'.feb', $account->feb))?'0':old('balance.'.$account->id.'.feb', $account->feb))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror
                            @endif
                        </td>
                        <td class="text-right">
                            @if($account->has_children==0)
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][mar]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_mar" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{fcurrency(empty(old('balance.'.$account->id.'.mar', $account->mar))?'0':old('balance.'.$account->id.'.mar', $account->mar))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror
                            @endif
                        </td>
                        <td class="text-right">
                            @if($account->has_children==0)
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][apr]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_apr" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{fcurrency(empty(old('balance.'.$account->id.'.apr', $account->apr))?'0':old('balance.'.$account->id.'.apr', $account->apr))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror
                            @endif
                        </td>
                        <td class="text-right">
                            @if($account->has_children==0)
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][may]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_may" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{fcurrency(empty(old('balance.'.$account->id.'.may', $account->may))?'0':old('balance.'.$account->id.'.may', $account->may))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror
                            @endif
                        </td>
                        <td class="text-right">
                            @if($account->has_children==0)
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][jun]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_jun" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{fcurrency(empty(old('balance.'.$account->id.'.jun', $account->jun))?'0':old('balance.'.$account->id.'.jun', $account->jun))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror
                            @endif
                        </td>
                        <td class="text-right">
                            @if($account->has_children==0)
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][jul]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_jul" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{fcurrency(empty(old('balance.'.$account->id.'.jul', $account->jul))?'0':old('balance.'.$account->id.'.jul', $account->jul))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror
                            @endif
                        </td>
                        <td class="text-right">
                            @if($account->has_children==0)
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][aug]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_aug" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{fcurrency(empty(old('balance.'.$account->id.'.aug', $account->aug))?'0':old('balance.'.$account->id.'.aug', $account->aug))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror
                            @endif
                        </td>
                        <td class="text-right">
                            @if($account->has_children==0)
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][sep]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_sep" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{fcurrency(empty(old('balance.'.$account->id.'.sep', $account->sep))?'0':old('balance.'.$account->id.'.sep', $account->sep))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror
                            @endif
                        </td>
                        <td class="text-right">
                            @if($account->has_children==0)
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][oct]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_oct" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{fcurrency(empty(old('balance.'.$account->id.'.oct', $account->oct))?'0':old('balance.'.$account->id.'.oct', $account->oct))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror
                            @endif
                        </td>
                        <td class="text-right">
                            @if($account->has_children==0)
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][nov]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_nov" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{fcurrency(empty(old('balance.'.$account->id.'.nov', $account->nov))?'0':old('balance.'.$account->id.'.nov', $account->nov))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror
                            @endif
                        </td>
                        <td class="text-right">
                            @if($account->has_children==0)
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][dec]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_dec" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{fcurrency(empty(old('balance.'.$account->id.'.dec', $account->dec))?'0':old('balance.'.$account->id.'.dec', $account->dec))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror
                            @endif
                        </td>
                        <td class="text-right">
                            @if($account->has_children==0)
                            <input style="width:150px" readonly @if($account->has_children==0) name="budget[{{$account->id}}][total]"@endif type="text" id="balance_{{$account->id}}_total" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':''}}" value= "{{fcurrency(old('balance.'.$account->id.'.total', $account->total))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
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
                <input type="hidden" name="budget_year" value="{{$budget_year}}" />
                @if(!empty(request('account_type_id')))
            @foreach (request('account_type_id') as $item)
            <input type="hidden" name="account_type_id[]" value="{{$item}}" />
            @endforeach
            @endif
            @if(!empty(request('account')))
            @foreach (request('account') as $item)
            <input type="hidden" name="account[]" value="{{$item}}" />
            @endforeach
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
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.3.0/css/fixedColumns.bootstrap4.min.css">
<script type="text/javascript" src="//unpkg.com/xlsx/dist/shim.min.js"></script>
<script type="text/javascript" src="//unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<script type="text/javascript" src="//unpkg.com/blob.js@1.0.1/Blob.js"></script>
<script type="text/javascript" src="//unpkg.com/file-saver@1.3.3/FileSaver.js"></script>
<style>
.hide{
  display:none;
}
.dataTables_length{
  padding-top:0.85em;
}
.hover{
  color:#212529;background-color:rgba(0,0,0,.075);
}

th, td { white-space: nowrap; }

</style>
@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/inputmask/min/jquery.inputmask.bundle.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
<script src="https://cdn.datatables.net/fixedcolumns/3.3.0/js/dataTables.fixedColumns.min.js"></script>
<script type="text/javascript">
$(function(){
    var dtconfig = {
        scrollY:        "400px",
        scrollX:        true,
        scrollCollapse: true,
        paging:         false,
        fixedColumns:   {
            leftColumns: 3
        },
      paging:   false,
      ordering: false,
      info:     false,
      dom:"<'row mb-2'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6 text-right hide'B>>"+
        "<'row'<'col-sm-12'tr>>"+
        "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4 text-center'i><'col-sm-12 col-md-4'p>>",

      language: {
          "emptyTable": "Tidak ada data",
          "info": "_START_ - _END_ dari _TOTAL_ baris",
          "infoEmpty": "",
          "lengthMenu":   "_MENU_ baris",
          "search": "Cari:",
          "zeroRecords": "Tidak ditemukan data yang sesuai",
          "paginate": {
            "first": "<<",
            "last": ">>",
            "next": ">",
            "previous": "<"
          }
        },
    };
    $('#data-table').DataTable(dtconfig);
    $('.balance').change(function(){
        var idx = $(this).attr('data-index');
        var total =0;
        $('input[data-index='+idx+']').each(function (item){
            total += parseNumber($(this).val());
        })
        $('#balance_'+idx+'_total').val(formatNumber(total));
    })

    $('.currency').inputmask({ 'alias': 'currency' })
    $('[data-mask]').inputmask();
    $(".select2").select2({theme: 'bootstrap4'})

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
    $('#dt-btn-pdf').on('click', function(e){
        generatePDF().download('budget.pdf')
    })
    $('#dt-btn-print').on('click', function(e){
        generatePDF().print()
    })
    $('#dt-btn-excel').on('click', function(e){
        generateExcel('xlsx')
    })
    $('#dt-btn-csv').on('click', function(e){
        generateExcel('csv')
    })
})

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
    return parseFloat(val.split('.').join('').split(',').join('.'));
}
function formatNumber(val){
    val = val.toString();
    if(val=='' || val==null||val==0)return '0,00';
    if(val.includes('.')){
        return val.split('.').join(',');
    }else{
        return val.split('.').join(',')+',00';
    }
}

function getData(){
    var data = [];
    var i = 1;
    $('#tdata>tr').each(function(){
        var row = []
        var level = $(this).attr('data-tree-level');
        var sp = level==0?'':(level==1?'\u200B\t':(level==2?'\u200B\t\t':''));
        var color = i%2==0?'white':'#f3f3f3';
        $('td', this).each(function(idx){
            if(idx==0){
                row.push({text: sp+$(this).html(), fillColor:color})
            }else if(idx==1){
                row.push({text: $(this).html(), fillColor:color})
            }else{
                var val = $('input',this).val()
                val = val?val:''
                row.push({text: sp+val, fillColor:color, alignment:'right'})
            }
        })
        data.push(row)
        i++
    })
    return data;
}
function generateExcel(type){
    var year = $('#budget_year').val();
    var dept = $('#department_id option:selected').text();
    dept = dept==''?'':'Departemen: '+dept;
    var data = getData();
    var dataExcel = data.map(function(a){
        return a.map(function(b){
            return b.text
        })
    })
    if(type=='csv'){
        dataExcel = [
            ['Kode Akun', 'Nama Akun', 'Saldo Awal'],
            ...dataExcel
        ]
    }else{
        dataExcel = [
            [`{{company('name')}}`],
            ['Daftar Akun'],
            [`Tanggal Laporan: {{date('d/m/Y H:i:s')}}`],
            [],
            [`{{empty($accountType)?'':'Tipe Akun: '.(implode(',', $accountType))}}`],
            [dept],
            [],
            ['Kode Akun', 'Nama Akun', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', year],
            ...dataExcel
        ]
    }

    var name = 'budgets';
    /* create new workbook */
    var wb = XLSX.utils.book_new();
    /* convert table 'table1' to worksheet named "Sheet1" */
    var ws = XLSX.utils.aoa_to_sheet(dataExcel);
    XLSX.utils.book_append_sheet(wb, ws, name);
    XLSX.writeFile(wb, name+'.'+type);
}
function generatePDF(){
    var fonts = {
	Roboto: {
		normal: 'fonts/Roboto-Regular.ttf',
		bold: 'fonts/Roboto-Medium.ttf',
		italics: 'fonts/Roboto-Italic.ttf',
		bolditalics: 'fonts/Roboto-MediumItalic.ttf'
	}
};
var year = $('#budget_year').val();
var account_type = $('#account_type option:selected').html();
var department = $('#department_id option:selected').html();
department = department?`{{__('Department')}}: ${department}`:'';
var data = getData();
var docDefinition = {
    pageOrientation: "landscape",
	content: [
		{
            text:`{{company('name')}}`, alignment:'left', fontSize: 12, bold: true, lineHeight: 2
        },
		{
            text:`{{__('Budget')}} ${year}`, alignment:'left', fontSize: 11, bold: true
        },
		{
            text:`{{__('Account Type')}}: ${account_type}`, alignment:'left', fontSize: 10, lineHeight: 1
        },
		{
            text: department, alignment:'left', fontSize: 10, lineHeight: 1
        },
		{
            text:`Tanggal Laporan: {{date('d/m/Y H:i:s')}}`, alignment:'left', fontSize: 10, lineHeight: 1
        },
		{
			style: 'tableExample',
			table: {
                widths: ['auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto'],
				headerRows: 1,
				body: [
					[{text:'Kode Akun', style:"tableHeader"}, {text:'Nama Akun', style:"tableHeader"},
                    {text:'Jan', style:"tableHeader", alignment:'right'},
                    {text:'Feb', style:"tableHeader", alignment:'right'},
                    {text:'Mar', style:"tableHeader", alignment:'right'},
                    {text:'Apr', style:"tableHeader", alignment:'right'},
                    {text:'Mei', style:"tableHeader", alignment:'right'},
                    {text:'Jun', style:"tableHeader", alignment:'right'},
                    {text:'Jul', style:"tableHeader", alignment:'right'},
                    {text:'Agu', style:"tableHeader", alignment:'right'},
                    {text:'Sep', style:"tableHeader", alignment:'right'},
                    {text:'Okt', style:"tableHeader", alignment:'right'},
                    {text:'Nov', style:"tableHeader", alignment:'right'},
                    {text:'Des', style:"tableHeader", alignment:'right'},
                    {text:year, style:"tableHeader", alignment:'right'},
                    ],
                    ...data
                ]
			},
			layout: 'noBorders'
		}
	],
    footer : function(currentPage, pageCount) {
                return [
                    { text: currentPage.toString() + ' / ' + pageCount, alignment: 'center' }
                ];
              },
	styles: {
		header: {
			fontSize: 18,
			bold: true,
			margin: [0, 0, 0, 10]
		},
		subheader: {
			fontSize: 16,
			bold: true,
			margin: [0, 10, 0, 5]
		},
		tableExample: {
			margin: [0, 10, 0, 5]
		},
		tableHeader: {
			alignment: "left",
            bold: true,
            color: "#212529",
            fillColor: "#d6d8db",
            fontSize: 10,
            border: [true, true, true, true],
		},
        tableBodyOdd: {fillColor: "#f3f3f3"}
	},
	defaultStyle: {
        fontSize: 9
		// alignment: 'justify'
	}
};
return pdfMake.createPdf(docDefinition)
}
</script>

@endpush
