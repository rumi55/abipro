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
        <div id="filter" class="row collapse ">
            <div class="form-group col">
                <label>Tipe Akun</label>
                <select id="filter_account_type" class="form-control select2" multiple>
                    @foreach($accountTypes as $type)
                        <option value="{{$type->id}}">{{tt($type, 'name')}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col">
                <label>Akun</label>
                <select id="filter_account" class="form-control select2" multiple>
                    @foreach($accounts as $account)
                        @if($account->tree_level==0)
                            <option value="{{$account->id}}">{{$account->account_name}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group col">
                <label>Subakun</label>
                <select id="filter_subaccount" class="form-control select2" multiple>
                    @foreach($accounts as $account)
                        @if($account->tree_level==1)
                            <option value="{{$account->id}}">{{$account->account_name}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
        </div>
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
        <button id="toggle-filter" class="btn btn-sm btn-secondary"  data-toggle="collapse" data-target="#filter" aria-expanded="false" aria-controls="filter"  ><i class="fas fa-filter"></i> {{__("Filter")}}</button>
        <div class="table-responsive mt-4">
            <table  class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th style="width:20px">
                            <a id="collapse-all" class="collapse-btn" href="javascript:void(0)" data-collapse="false">
                                <i class="fas fa-angle-down fa-xs"></i>
                            </a>
                        </th>
                        <th>{{__('Account No.')}}</th>
                        <th>{{__('Account Name')}}</th>
                        <th>{{__('Type')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="tdata">
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
                    <tr id="row-{{$account->id}}" class="tr-row" data-id="{{$account->id}}" data-tree-level="{{$account->tree_level}}" data-has-children="{{$account->has_children}}" data-parent="{{$account->account_parent_id}}"  data-type="{{$account->account_type_id}}">
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

                                <form method="POST" action="{{route('accounts.delete',$account->id)}}" style="display:inline">
                                    @method('DELETE') @csrf
                                    <button type="button" class="dropdown-item btn-delete"><i class="fas fa-trash"></i> {{__('Delete')}}</button>
                                </form>
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
@push('css')
<link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush
@push('js')
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="//unpkg.com/xlsx/dist/shim.min.js"></script>
<script type="text/javascript" src="//unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<script type="text/javascript" src="//unpkg.com/blob.js@1.0.1/Blob.js"></script>
<script type="text/javascript" src="//unpkg.com/file-saver@1.3.3/FileSaver.js"></script>
<script type="text/javascript">

var accounts =JSON.parse(`{!! json_encode($accounts) !!}`);
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
function filterAccountType(){
    var val = accountType;
    var vlen = val.length;
        if(vlen>0){
            $('.tr-row').each(function(){
                var t = $(this).attr('data-type');
                if(val.find(function(v){return v==t})){
                    $(this).show();
                }else{
                    $(this).hide();
                }
            })
        }else{
            $('.tr-row').show();
        }
}
function filterAccount(){
    var val = selectedAccounts;
    var vlen = val.length;
    if(vlen>0){
        $('.tr-row').each(function(){
            var t = $(this).attr('data-tree-level')==0?$(this).attr('data-id'):$(this).attr('data-parent');
            if(val.find(function(v){return v==t})){
                $(this).show();
            }else{
                $(this).hide();
            }
        })
    }else{
        filterAccountType()
    }
    filterSubaccount(val);
}
$(function () {
    $('.select2').select2({theme: 'bootstrap4'});
    $('#filter_account_type').change(function(){
        var val = $(this).val();
        accountType = val;
        var filtered = [];
        var vlen = val.length;
        filterAccountType();
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
        filterAccount();
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
    $('#filter_subaccount').change(function(){
        var val = $(this).val();
        var vlen = val.length;
        if(vlen>0){
            $('.tr-row').each(function(){
                var t = $(this).attr('data-id');
                if(val.find(function(v){return v==t})){
                    $(this).show();
                }else{
                    $(this).hide();
                }
            })
        }else{
            filterAccount(val)
        }
    });

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
    $('#collapse-all').click(function(){
        var isCollapse = $(this).attr('data-collapse');
        isCollapse = isCollapse=='true'?false:true;
        var icon = isCollapse?'right':'down';
        $(this).html(`<i class="fas fa-angle-${icon} fa-xs"></i>`)
        collapseAll(isCollapse);
        $(this).attr('data-collapse', isCollapse)
    })
    $('#dt-btn-pdf').on('click', function(e){
        generatePDF().download('chart_of_account.pdf')
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
function getData(){
    var data = [];
    var i = 1;
    $('#tdata>tr').each(function(){
        var row = []
        var level = $(this).attr('data-tree-level');
        var sp = level==0?'':(level==1?'\u200B\t':(level==2?'\u200B\t\t':'\u200B\t\t\t'))
        if(!($(this).css('display') == 'none')){
            var color = i%2==0?'white':'#f3f3f3'
            $('td', this).each(function(idx){
                if(idx==1){
                    row.push({text: sp+$(this).html(), fillColor:color})
                }else if(idx==2){
                    row.push({text: sp+$('a',this).html(), fillColor:color})
                }else if(idx==3){
                    row.push({text: $(this).html(), fillColor:color})
                }
            })
            data.push(row)
            i++
        }
    })
    return data;
}
function generateExcel(type){
    var data = getData();
    var dataExcel = data.map(function(a){
        return a.map(function(b){
            return b.text
        })
    })
    if(type=='csv'){
        dataExcel = [
            ['Kode Akun', 'Nama Akun', 'Tipe'],
            ...dataExcel
        ]
    }else{
        dataExcel = [
            [`{{company('name')}}`],
            ['Daftar Akun'],
            [`Tanggal Laporan: {{date('d/m/Y H:i:s')}}`],
            [],
            ['Kode Akun', 'Nama Akun', 'Tipe'],
            ...dataExcel
        ]
    }

    var name = 'chart_of_accounts';
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

var data = getData();

var docDefinition = {
	content: [
		{
            text:`{{company('name')}}`, alignment:'left', fontSize: 12, bold: true, lineHeight: 2
        },
		{
            text:`Daftar Akun`, alignment:'left', fontSize: 11, bold: true
        },
		{
            text:`Tanggal Laporan: {{date('d/m/Y H:i:s')}}`, alignment:'left', fontSize: 10, lineHeight: 2
        },
		{
			style: 'tableExample',
			table: {
                widths: ['auto', '*', '*'],
				headerRows: 1,
				body: [
					[{text:'Kode Akun', style:"tableHeader"}, {text:'Nama Akun', style:"tableHeader"}, {text:'Tipe Akun', style:"tableHeader"}],
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
