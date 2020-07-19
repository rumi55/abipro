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

<div class="card">
    <div class="card-body">
    <form action="{{route('accounts.budgets')}}" method="get">
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
                <tbody>
                @php $type = null; @endphp
                @foreach($accounts as $account)
                    @if($type!=$account->account_type_id)
                    @php $type = $account->account_type_id; @endphp
                    
                    @endif
                    <tr id="row-{{$account->id}}" class="tr-row" data-id="{{$account->id}}" data-parent="{{$account->account_parent_id}}">
                        @if($account->tree_level==1)
                        <td class="pl-4" style="vertical-align:middle;">{{$account->account_no}}</td>
                        @elseif($account->tree_level==2)
                        <td class="pl-5" style="vertical-align:middle;">{{$account->account_no}}</td>
                        @else
                        <td style="vertical-align:middle;">{{$account->account_no}}</td>
                        @endif
                        <td style="vertical-align:middle;">{{$account->account_name}}</td>
                        <td class="text-right">
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][jan]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}"  type="text" id="balance_{{$account->id}}_jan" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{old('balance.'.$account->id.'.jan', $account->jan)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @if($account->has_children==0)
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror                                
                            @endif
                        </td>
                        <td class="text-right">
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][feb]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_feb" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{old('balance.'.$account->id.'.feb', $account->feb)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @if($account->has_children==0)
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror                                
                            @endif
                        </td>
                        <td class="text-right">
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][mar]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_mar" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{old('balance.'.$account->id.'.mar', $account->mar)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @if($account->has_children==0)
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror                                
                            @endif
                        </td>
                        <td class="text-right">
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][apr]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_apr" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{old('balance.'.$account->id.'.apr', $account->apr)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @if($account->has_children==0)
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror                                
                            @endif
                        </td>
                        <td class="text-right">
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][may]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_may" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{old('balance.'.$account->id.'.may', $account->may)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @if($account->has_children==0)
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror                                
                            @endif
                        </td>
                        <td class="text-right">
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][jun]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_jun" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{old('balance.'.$account->id.'.jun', $account->jun)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @if($account->has_children==0)
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror                                
                            @endif
                        </td>
                        <td class="text-right">
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][jul]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_jul" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{old('balance.'.$account->id.'.jul', $account->jul)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @if($account->has_children==0)
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror                                
                            @endif
                        </td>
                        <td class="text-right">
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][aug]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_aug" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{old('balance.'.$account->id.'.aug', $account->aug)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @if($account->has_children==0)
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror                                
                            @endif
                        </td>
                        <td class="text-right">
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][sep]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_sep" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{old('balance.'.$account->id.'.sep', $account->sep)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @if($account->has_children==0)
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror                                
                            @endif
                        </td>
                        <td class="text-right">
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][oct]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_oct" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{old('balance.'.$account->id.'.oct', $account->oct)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @if($account->has_children==0)
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror                                
                            @endif
                        </td>
                        <td class="text-right">
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][nov]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_nov" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{old('balance.'.$account->id.'.nov', $account->nov)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @if($account->has_children==0)
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror                                
                            @endif
                        </td>
                        <td class="text-right">
                            <input style="width:150px" {{$account->has_children==1?'readonly':''}} @if($account->has_children==0) name="budget[{{$account->id}}][dec]"@endif data-index="{{$account->id}}" data-parent="{{$account->account_parent_id}}" type="text" id="balance_{{$account->id}}_dec" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':' balance'}}" value= "{{old('balance.'.$account->id.'.dec', $account->dec)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @if($account->has_children==0)
                            @error('balance_'.$account->id)<small class="text-danger">{!! $message !!}</small>@enderror                                
                            @endif
                        </td>
                        <td class="text-right">
                            <input style="width:150px" readonly @if($account->has_children==0) name="budget[{{$account->id}}][total]"@endif type="text" id="balance_{{$account->id}}_total" tab="-1" class="form-control{{$account->has_children==1?'-plaintext':''}}" value= "{{old('balance.'.$account->id.'.total', $account->total)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @if($account->has_children==0)
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
                <input type="hidden" name="account_type_id" value="{{$account_type_id}}" />
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
        $('#balance_'+idx+'_total').val(total);
    })
    
    $('.currency').inputmask({ 'alias': 'currency' })
    $('[data-mask]').inputmask();
    $(".select2").select2({theme: 'bootstrap4'})
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
            $('#account_type').val('{{request("account_type_id", 1)}}')
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
    return parseInt(val.split('.').join(''));
}
</script>

@endpush