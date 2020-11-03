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
@php $accountType =[];$dept=''; @endphp
<div id="filter" class="card">
    <div class="card-body">
    <form action="{{route('accounts.opening_balance')}}" method="get">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="account_type" >{{__('Account Type')}}</label>
                    <select id="account_type" name="account_type_id[]" class="form-control select2" multiple>
                        @foreach($accountTypes as $type)
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
            <div class="col-md-4">
                <div class="form-group">
                    <label for="department_id" >{{__('Department')}}</label>
                    <select id="department_id" name="department_id" class="form-control select2"></select>
                </div>
            </div>
            <div class="col-md-4">
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
        <div class="btn-group">
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

        <table class="table table-hover table-sm mt-4">
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
                <tbody id="tdata">
                @php $type = null; @endphp
                @foreach($accounts as $account)
                    @if($type!=$account->account_type_id)
                    @php $type = $account->account_type_id; @endphp
                    <tr class="type-name">
                        <td class="font-weight-bold" colspan="4">{{$account->account_type_name}}</td>
                    </tr>
                    @endif
                <tr id="row-{{$account->id}}" class="tr-row" data-tree-level="{{$account->tree_level}}" data-id="{{$account->id}}" data-parent="{{$account->account_parent_id}}">
                        @if($account->tree_level==1)
                        <td  class="pl-4" style="vertical-align:middle;width:20%">{{$account->account_no}}</td>
                        @elseif($account->tree_level==2)
                        <td class="pl-5" style="vertical-align:middle;width:20%">{{$account->account_no}}</td>
                        @else
                        <td style="vertical-align:middle;width:20%">{{$account->account_no}}</td>
                        @endif
                        <td style="vertical-align:middle;width:60%">{{$account->account_name}}</td>
                        <td class="text-right" style="width:20%">
                            @if($account->has_children==0)
                            <input style="width:200px" name="balance[{{$account->id}}]" data-index="{{$account->id}}" type="text" id="balance_{{$account->id}}" class="form-control" value="{{fcurrency(empty(old('balance.'.$account->id, $account->balance))?'0':old('balance.'.$account->id, $account->balance))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
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
@endpush
@push('js')
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/inputmask/min/jquery.inputmask.bundle.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="//unpkg.com/xlsx/dist/shim.min.js"></script>
<script type="text/javascript" src="//unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<script type="text/javascript" src="//unpkg.com/blob.js@1.0.1/Blob.js"></script>
<script type="text/javascript" src="//unpkg.com/file-saver@1.3.3/FileSaver.js"></script>
<script type="text/javascript">
var accounts =JSON.parse(`{!! json_encode($paccounts) !!}`);
var accountType = [];
var selectedAccounts = [];
function filterSubaccount(parentID){
    var l = parentID.length;
    filtered = accounts.filter(function(item){
        if(l>0){
            return parentID.find(function(v){return v==item.account_parent_id})!=null;
        }else{
            return item.tree_level==1;
        }
    }).map(function(item){
        return {
            id:item.id,
            text: '('+item.account_no+') '+item.account_name
        }
    });
    $('#filter_subaccount').html('');
    $('#filter_subaccount').select2('destroy');
    $('#filter_subaccount').select2({theme: 'bootstrap4', data:filtered})
}

$(function(){
    $('.select2').select2({theme: 'bootstrap4'});
    $('#account_type').change(function(){
        var val = $(this).val();
        accountType = val;
        var filtered = [];
        var vlen = val.length;
        filtered = accounts.filter(function(item){
            if(vlen>0){
                return val.find(function(v){return v==item.account_type_id})!=null && item.tree_level==0;
            }else{
                return item.tree_level==0;
            }
        }).map(function(item){
            return {
                id:item.id,
                text: '('+item.account_no+') '+item.account_name
            }
        });
        $('#filter_account').html('');
        $('#filter_account').select2('destroy');
        $('#filter_account').select2({theme: 'bootstrap4', data:filtered});
        filterSubaccount([]);
    })
    $('#filter_account').change(function(){
        selectedAccounts = $(this).val();
        filterSubaccount(selectedAccounts);
        // var vlen = val.length;
        // if(vlen>0){
        //     $('.tr-row').each(function(){
        //         var t = $(this).attr('data-tree-level')==0?$(this).attr('data-id'):$(this).attr('data-parent');
        //         if(val.find(function(v){return v==t})){
        //             $(this).show();
        //         }else{
        //             $(this).hide();
        //         }
        //     })
        // }else{
        //     filterAccountType()
        // }

    });
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
    $('#dt-btn-pdf').on('click', function(e){
        generatePDF().download('opening_balance.pdf')
    })
    $('#dt-btn-print').on('click', function(e){
        generatePDF().print()
    });
    $('#dt-btn-excel').on('click', function(e){
        generateExcel('xlsx')
    })
    $('#dt-btn-csv').on('click', function(e){
        generateExcel('csv')
    })
})
function onchange(){
    var debit = sum('.debit');
    var credit = sum('.credit');
    $('#total_debit').val(formatNumber(debit));
    $('#total_credit').val(formatNumber(credit));
    if(debit==credit){
        $('.total').addClass('text-success');
        $('.total').removeClass('text-danger');
        $('#total').val(formatNumber(debit));
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
        var sp = level==0?'':(level==1?'\u200B\t':(level==2?'\u200B\t\t':''))
        var color = i%2==0?'white':'#f3f3f3'
        if($(this).hasClass('type-name')){
            $('td', this).each(function(idx){
                row.push({text: sp+$(this).html(), fillColor:color, bold:true, colSpan:3})
            })
        }else{

            $('td', this).each(function(idx){
                if(idx==0){
                    row.push({text: sp+$(this).html(), fillColor:color})
                }else if(idx==1){
                    row.push({text: $(this).html(), fillColor:color})
                }else if(idx==2){
                    var val = $('input',this).val()
                    val = val?val:''
                    row.push({text: sp+val, fillColor:color, alignment:'right'})
                }
            })
        }
        data.push(row)
        i++
    })
    return data;
}
function generateExcel(type){
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
            ['Kode Akun', 'Nama Akun', 'Saldo Awal'],
            ...dataExcel
        ]
    }

    var name = 'opening_balance';
    /* create new workbook */
    var wb = XLSX.utils.book_new();
    /* convert table 'table1' to worksheet named "Sheet1" */
    var ws = XLSX.utils.aoa_to_sheet(dataExcel);
    console.log(ws)
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

var dept = $('#department_id option:selected').text();
dept = dept==''?'':'Departemen: '+dept;
var data = getData();
var account_type = $('#account_type option:selected').html();
var department = $('#department_id option:selected').html();
department = department?`{{__('Department')}}: ${department}`:'';
account_type = account_type?`{{__('Account Type')}}: ${account_type}`:'';
var docDefinition = {
	content: [
		{
            text:`{{company('name')}}`, alignment:'left', fontSize: 12, bold: true, lineHeight: 2
        },
		{
            text:`Saldo Awal`, alignment:'left', fontSize: 11, bold: true
        },
		{
            text:`Tanggal Laporan: {{date('d/m/Y H:i:s')}}`, alignment:'left', fontSize: 10, lineHeight: 2
        },
		{
            text:`{{empty($accountType)?'':'Tipe Akun: '.(implode(',', $accountType))}}`, alignment:'left', fontSize: 10
        },
		{
            text:dept, alignment:'left', fontSize: 10
        },
		{
			style: 'tableExample',
			table: {
                widths: ['auto', '*', '*'],
				headerRows: 1,
				body: [
					[{text:'Kode Akun', style:"tableHeader"}, {text:'Nama Akun', style:"tableHeader"}, {text:'Saldo Awal', style:"tableHeader", alignment:'right'}],
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
            fontSize: 11,
            border: [true, true, true, true],
		},
        tableBodyOdd: {fillColor: "#f3f3f3"}
	},
	defaultStyle: {
        fontSize: 10
		// alignment: 'justify'
	}
};
return pdfMake.createPdf(docDefinition)
}
</script>

@endpush
