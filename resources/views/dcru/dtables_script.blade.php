@if(count($filter)>0)
@endif
@include('dcru._dtfilter', ['fields'=>$columns])
@include('dcru._dtcolumns')
<div class="table-responsive-sm">
  <table id="{{$dtname}}" style="width:100%" class="table table-hover table-strip">
      <thead class="thead-light">
          <tr>
              @foreach($columns as $column)
                @if(!($column['type']=='checkbox' && count($bulk_actions)==0))
                  @if($column['type']=='checkbox')
                  <th style="width:20px" scope="col" class="noexport"><input type="checkbox" class="check-all"/></th>
                  @elseif($column['type']=='menu')
                  <th style="width:20px" scope="col" class="noexport"></th>
                  @elseif(!(isset($column['visible']) && $column['visible']==false))
                    <th scope="col" >{{__($column['title'])}}</th>
                  @endif
                @endif
              @endforeach
          </tr>
      </thead>
  </table>
</div>

<?php
$cb_actions = 0;
$bactions = '';
foreach($bulk_actions as $act){
  if(isset($act['type']) && $act['type']=='delete' && has_action($name, 'delete')){
    $bactions .= '<form action="'.asset(route('dcru.delete.all', ['name'=>$name], false)).'" method="POST" style="display:inline;">'.csrf_field().'<button type="button" data-confirm="true" data-confirm-text="'.__('Are you sure want to delete the selected items?').'" class="dropdown-item bulk-btn"><i class="fas fa-trash"></i> Hapus</button></form>';
    $cb_actions++;
  }else{
    $routes = explode('.', $act['route']['name']);
    if(has_action($routes[0], $routes[1])){
      $params = isset($act['route']['params'])?$act['route']['params']:[];
      $method = isset($act['route']['method'])?$act['route']['method']:'POST';
      $bactions .= '<form action="'.asset(route($act['route']['name'], $params, false)).'" method="'.$method.'" style="display:inline;">'.csrf_field().'<button type="button" data-confirm="'.(isset($act['confirm'])?true:false).'" '.(isset($act['confirm'])?('data-confirm-text="'.__($act['confirm']).'"'):'').' class="dropdown-item bulk-btn">'.(isset($act['icon'])?('<i class="'.$act['icon'].'"></i> '):'') .$act['label'].'</button></form>';
      $cb_actions++;
    }
  }
}
?>


@push('js')
<?php
//get parameters
$route = Route::current();
$parameters = $route->parameters();
$str_data = '';
foreach($parameters as $key=>$param){
  $str_data = $key.': "'.$param.'", ';
}
?>
<script>
    var printerCounter = 0;
    var {{$dtname}}config = {
      processing: true,
      serverSide: true,
      ajax: {
        url: "{!! asset(route('dcru.index.dt', ['name'=>$name], false)) !!}",
        data: { {!! $str_data !!} dtname:"{{$dtname}}"}
      },
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
      columns:[
        @foreach($dtcolumns as $dt)
          @if(!($dt['type']=='checkbox' && $cb_actions==0))
          {
            data:"{{$dt['data']}}",
            name:"{{$dt['name']}}",
            @if(isset($dt['class']))
            className:"{{$dt['class']}}",
            @elseif(in_array($dt['type'], ['boolean', 'icon', 'badge', 'image']))
            className:"text-center",
            @elseif(in_array($dt['type'], ['number', 'currency']))
            className:"text-right number",
            @elseif(in_array($dt['type'], ['date', 'date']))
            className:"date",
            @endif
            @if($dt['type']!='checkbox')
            title:"{{__($dt['title'])}}",
            @endif
            @if($dt['type']=='checkbox'||$dt['type']=='menu')
            searchable:false,
            orderable:false,
            @else
            searchable:{{$dt['searchable']==1?'true':'false'}},
            orderable:{{$dt['orderable']==1?'true':'false'}},
            visible:{{$dt['visible']==true?'true':'false'}}
            @endif
          },
          @endif
        @endforeach
      ],
      dom:"<'row mb-2'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6 text-right hide'B>>"+
        "<'row'<'col-sm-12 col-md-7 mb-2 bulk-actions-wrapper-{{$dtname}}'><'col-sm-12 col-md-5 mb-2'f>>"+
        "<'row'<'col-sm-12'tr>>"+
        "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4 text-center'i><'col-sm-12 col-md-4'p>>",
      buttons: [
        {
            extend: 'copyHtml5',
            title:'{{__($dt_title)}}',
            exportOptions: {
              columns: ':visible:not(.noexport)'
            }
        },
        {
            extend: 'csvHtml5',
            title:'{{__($dt_title)}}',
            exportOptions: {
              columns: ':visible:not(.noexport)'
            }
        },
        {
            extend: 'excelHtml5',
            title:'{{__($dt_title)}}',
            exportOptions: {
              columns: ':visible:not(.noexport)'
            }
        },
        {
            extend: 'pdfHtml5',
            title: '',
            download: 'print',
            customize: function (doc) {
                doc.content = [
                    {
                        text:`{{company('name')}}`, alignment:'left', fontSize: 12, bold: true, lineHeight: 2
                    },
                    {
                        text:`{{__($dt_title)}}`, alignment:'left', fontSize: 11, bold: true
                    },
                    {
                        text:`Tanggal Laporan: {{date('d/m/Y H:i:s')}}`, alignment:'left', fontSize: 10, lineHeight: 2
                    },
                    ...doc.content
                ]
                doc.styles.title = {alignment:'left', fontSize:11, bold: true}
                doc.styles.tableHeader= {
                    alignment: "left",
                    bold: true,
                    color: "white",
                    fillColor: "#2d4154",
                    fontSize: 11
                }
              doc.footer = function(currentPage, pageCount) {
                return [
                    { text: currentPage.toString() + ' / ' + pageCount, alignment: 'center' }
                ];
              }
              var table = doc.content[3].table
              var len = doc.content[3].table.body[0].length;
              table.widths = [];
              for(var i=0;i<len;i++){
                  if(i==len-1){
                    table.widths.push('auto')
                  }else{
                    table.widths.push('*')
                  }
              }
            },
            exportOptions: {
              columns: ':visible:not(.noexport)'
            }
        },
        {
            extend: 'print',
            title:'{{__($dt_title)}}',
            exportOptions: {
              columns: ':visible:not(.noexport)'
            }
        },
    ],
      language: {
          "emptyTable": "{{__('No Data')}}",
          "info": "_START_ - _END_ {{__('of')}} _TOTAL_ {{__('records')}}",
          "infoEmpty": "",
          "lengthMenu":   "_MENU_ {{__('records')}}",
          "search": "{{__('Search')}}:",
          "zeroRecords": "Tidak ditemukan data yang sesuai",
          "paginate": {
            "first": "<",
            "last": ">",
            "next": ">>",
            "previous": "<<"
          }
        },
    };
  $(function () {
    load{{$dtname}}();
    $('#filter-{{$dtname}}').submit(function(e){
      e.preventDefault();
      var form = $(this).serializeArray();
      var data = [];
      for(var i=0;i<form.length;i++){
        var d = form[i]
        if(d.value==""){
          continue;
        }
        if(d.name.endsWith('[]')){
          var name = d.name.replace('[]', '');
          var f = data.find(function(item){
            return item.name==name;
          });
          if(f==undefined){
            data.push({
              name:name,
              value: [d.value]
            })
          }else{
            f.value.push(d.value)
          }
        }else if(d.name.endsWith('[start]')){
          var name = d.name.replace('[start]', '');
          var f = data.find(function(item){
            return item.name==name;
          });
          if(f==undefined){
            data.push({
              name:name,
              value: {start:d.value}
            })
          }else{
            f.value.start = d.value;
          }
        }else if(d.name.endsWith('[end]')){
          var name = d.name.replace('[end]', '');
          var f = data.find(function(item){
            return item.name==name;
          });
          if(f==undefined){
            if(d.value!=""){
            data.push({
              name:name,
              value: {end:d.value}
            })
            }
          }else{
            f.value.end = d.value;
          }
        }else{
          data.push({name:d.name, value:d.value})
        }
      }
      load{{$dtname}}(data);
    });
    $('#{{$dtname}}').on( 'draw.dt', function () {
      $('.bulk-actions-wrapper-{{$dtname}}').html(
        '<div class="btn-group">'+
          '<button id="dt-btn-print-{{$dtname}}" type="button" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> {{__("Print")}}</button>'+
        //   '<button id="dt-btn-pdf-{{$dtname}}" type="button" class="btn btn-secondary btn-sm"><i class="fas fa-file-pdf"></i> PDF</button>'+
          '<button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown">'+
            '<span class="sr-only">Toggle Dropdown</span>'+
            '<div class="dropdown-menu" role="menu">'+
              '<a id="dt-btn-pdf-{{$dtname}}" class="dropdown-item" href="#"><i class="fas fa-file-pdf"></i> PDF</a>'+
              '<a id="dt-btn-excel-{{$dtname}}" class="dropdown-item" href="#"><i class="fas fa-file-excel"></i> Excel</a>'+
              '<a id="dt-btn-csv-{{$dtname}}" class="dropdown-item" href="#"><i class="fas fa-file-csv"></i> CSV</a>'+
            //   '<a id="dt-btn-copy-{{$dtname}}" class="dropdown-item" href="#"><i class="fas fa-copy"></i> Copy</a>'+
            '</div>'+
          '</button>'+
        '</div>'
      );
      $('#dt-btn-pdf-{{$dtname}}').click(function(){
        //   $('.buttons-pdf[aria-controls={{$dtname}}]').trigger('click')
        var pdf = generatePDF('{{$dtname}}');
        pdf.download('{{__($dt_title)}}')
    })
      $('#dt-btn-excel-{{$dtname}}').click(function(){
        //   $('.buttons-excel[aria-controls={{$dtname}}]').trigger('click')
        generateExcel('{{$dtname}}','xlsx')
        })
      $('#dt-btn-print-{{$dtname}}').click(function(){
        //   $('.buttons-print[aria-controls={{$dtname}}]').trigger('click')
        var pdf = generatePDF('{{$dtname}}');
        pdf.print()
        })
        $('#dt-btn-csv-{{$dtname}}').click(function(){
            // $('.buttons-csv[aria-controls={{$dtname}}]').trigger('click')
            generateExcel('{{$dtname}}','csv')
        })
      $('#dt-btn-copy-{{$dtname}}').click(function(){$('.buttons-copy[aria-controls={{$dtname}}]').trigger('click')})

      $('.bulk-actions-wrapper-{{$dtname}}').append(
        `
        <button id="toggle-columns-{{$dtname}}" class="btn btn-sm btn-secondary"  data-toggle="collapse" data-target="#columns-{{$dtname}}" aria-expanded="false" aria-controls="columns"  ><i class="fas fa-th"></i> {{__("Column")}}</button>
        `
      );
      $('#columns-{{$dtname}}').on('hide.bs.collapse', function () {
          $('#toggle-columns-{{$dtname}}').removeClass('active')
        })
      $('#columns-{{$dtname}}').on('show.bs.collapse', function () {
        $('#toggle-columns-{{$dtname}}').addClass('active')
      })
      $('.toggle-vis').on( 'click', function (e) {
        $('#{{$dtname}}').DataTable().column($(this).val()).visible($(this).prop('checked'));
      });

        $('.bulk-actions-wrapper-{{$dtname}}').append(
          `
          <button id="toggle-filter-{{$dtname}}" class="btn btn-sm btn-secondary"  data-toggle="collapse" data-target="#filter-{{$dtname}}" aria-expanded="false" aria-controls="filter"  ><i class="fas fa-filter"></i> {{__("Filter")}}</button>
          `
        );
        @if(request('filter_collapse')=='true')
          $('#toggle-filter-{{$dtname}}').addClass('active')
        @endif
        $('#filter-{{$dtname}}').on('hide.bs.collapse', function () {
          $('#toggle-filter-{{$dtname}}').removeClass('active')
        })
        $('#filter-{{$dtname}}').on('show.bs.collapse', function () {
          $('#toggle-filter-{{$dtname}}').addClass('active')
        })

      @if($cb_actions>0)
      $('.bulk-actions-wrapper-{{$dtname}}').append(
        '<div id="{{$dtname}}-dt-selected-id" style="display:inline"></div>'+
        '<div class="dropdown" style="display:inline;margin-left:5px">'+
        '<button id="bulk-actions-{{$dtname}}" style="display:none" type="button" class="btn btn-sm btn-danger dropdown-toggle" data-toggle="dropdown">{{__("Bulk Actions")}}</button>'+
        '<div class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="bulk-actions-{{$dtname}}">'+
        '{!! $bactions !!}'+
        '</div>'+
        '</div>'
      );
      @endif
      $(".check-row" ).change( function(){
        var id = $(this).val();
        if(this.checked){
            $('#{{$dtname}}-dt-selected-id').append('<input id="{{$dtname}}-id-'+id+'" name="id[]" type="hidden" value="'+id+'" >')
        }else{
            $('#{{$dtname}}-id-'+id).remove();
        }
        var check = 0;
          $( ".check-row:checked" ).each(function(){
            check++;
          });
          if(check>0){
            $('#bulk-actions-{{$dtname}}').show();
          }else{
            $('#bulk-actions-{{$dtname}}').hide();
          }
      });
      $('.btn-delete').click(function(e){
        var form = $(this).parent();
        Swal.fire({
          title: '{{__("Warning")}}!',
          icon: 'warning',
          html:'{{__("Are you sure want to delete the selected item?")}}',
          showCloseButton: false,
          showCancelButton: true,
          focusConfirm: false
        }).then(function(result){
          if(result.value){
            form.submit();
          }
        })
      });

      @if($cb_actions>0)
      $('.bulk-btn').click(function(e){
        e.preventDefault();
        var confirm = $(this).attr('data-confirm');
        var form = $(this).parent();
        if(confirm==1){
        var confirm_text = $(this).attr('data-confirm-text');
          Swal.fire({
            title: '{{__("Confirmation")}}',
            icon: 'warning',
            html: confirm_text,
            showCloseButton: false,
            showCancelButton: true,
            focusConfirm: false
          }).then(function(result){
            if(result.value){
              var id = $('#{{$dtname}}-dt-selected-id').html();
              form.append(id);
              form.submit();
            }
          })
        }else{
              var id = $('#{{$dtname}}-dt-selected-id').html();
              form.append(id);
          form.submit();
        }

      });
      @endif

    } );
    $('.check-all').change(function() {
          $( ".check-row" ).prop( "checked", this.checked );
          $( ".check-row" ).each(function(){
            var id = $(this).val();
            if(this.checked){
                $('#{{$dtname}}-dt-selected-id').append('<input id="id-'+id+'" name="id[]" type="hidden" value="'+id+'" >')
            }else{
                $('##{{$dtname}}-id-'+id).remove();
            }
          });
          if(this.checked){
            $('#bulk-actions-{{$dtname}}').show();
          }else{
            $('#bulk-actions-{{$dtname}}').hide();
          }
    });
  });
  function load{{$dtname}}(filter=[]){
    {{$dtname}}config.ajax.data.filter = filter;
    $('#{{$dtname}}').DataTable().destroy();
    var dt = $('#{{$dtname}}').DataTable({{$dtname}}config);

    $('#search').on('keyup change', function () {
        dt.search(this.value).draw();
    });
  }

function getData(dtid){
    var data = [];
    var exportable = [];
    $('#'+dtid+'>thead>tr').each(function(){
        var row = []
        $('th', this).each(function(idx){
            if(!$(this).hasClass('noexport')){
                var align = $(this).hasClass('text-center')?{alignment:'center'}:($(this).hasClass('text-right')?{alignment:'right'}:{})
                row.push({text: $(this).html(), style:"tableHeader",...align})
                exportable.push(idx)
            }
        })
        data.push(row)
    })
    var i = 1;
    $('#'+dtid+'>tbody>tr').each(function(){
        var row = []
        var color = i%2==0?'white':'#f3f3f3'
        $('td', this).each(function(idx){
            var align = $(this).hasClass('text-center')?{alignment:'center'}:($(this).hasClass('text-right')?{alignment:'right'}:{})
            var k = idx;
            var f = exportable.find(function(j){return j==k})
            if(f!=null){
                row.push({text: $(this).text(), fillColor:color, ...align})
            }
        })
        i++;
        data.push(row)
    })
    console.log(data);
    return data;
}
function getHeaderExcel(dtid){
    var data = [];
    var exportable = [];
    $('#'+dtid+'>thead>tr').each(function(){
        var row = []
        $('th', this).each(function(idx){
            if(!$(this).hasClass('noexport')){
                var align = $(this).hasClass('text-center')?{alignment:'center'}:($(this).hasClass('text-right')?{alignment:'right'}:{})

                row.push($(this).html())
                exportable.push(idx)
            }
        })
        data.push(row)
    })
    return data;
}
function getDataExcel(dtid){
    var data = [];
    var exportable = [];
    $('#'+dtid+'>thead>tr').each(function(){
        $('th', this).each(function(idx){
            if(!$(this).hasClass('noexport')){
                exportable.push(idx)
            }
        })
    })
    var i = 1;
    $('#'+dtid+'>tbody>tr').each(function(){
        var row = []
        var color = i%2==0?'white':'#f3f3f3'
        $('td', this).each(function(idx){
            var align = $(this).hasClass('text-center')?{alignment:'center'}:($(this).hasClass('text-right')?{alignment:'right'}:{})
            var f = exportable.find(function(j){return j==idx})
            if(f){
                var v = $(this).text()
                v = $(this).hasClass('number')?parseNumber(v):v
                row.push(v)
            }
        })
        i++;
        data.push(row)
    })
    return data;
}
function generateExcel(dtid,type){
    // var data = getData(dtid);
    var header = getHeaderExcel(dtid);
    var data = getDataExcel(dtid);
    // var dataExcel = data.map(function(a){
    //     return a.map(function(b){
    //         return b.text
    //     })
    // })
    var colNumber = header[0].length;
    var dataExcel = [
        ...header,
        ...data
    ];
    let objectMaxLength = []

    dataExcel.map(arr => {
        Object.keys(arr).map(key => {
            let value = arr[key] === null ? '' : arr[key]

            if (typeof value === 'number')
            {
            return objectMaxLength[key] = 15
            }

            objectMaxLength[key] = objectMaxLength[key] >= value.length ? objectMaxLength[key]  : value.length
        })
    })

    let worksheetCols = objectMaxLength.map(width => {
        return {
            width
        }
    })
    if(type=='xlsx'){
        dataExcel = [
            [`{{company('name')}}`],
            [],
            ['{{__($dt_title)}}'],
            [`Tanggal Laporan: {{date('d/m/Y H:i:s')}}`],
            [],
            ...dataExcel
        ]
    }

    var name = '{{__($dt_title)}}';
    /* create new workbook */
    var wb = XLSX.utils.book_new();
    /* convert table 'table1' to worksheet named "Sheet1" */
    var ws = XLSX.utils.aoa_to_sheet(dataExcel);
    ws["!cols"] = worksheetCols;
    // var merge = { s: {r:0, c:0}, e: {r:0, c:1} };
    if(!ws['!merges']) ws['!merges'] = [];
    ws['!merges'].push({ s: {r:0, c:0}, e: {r:0, c:colNumber-1} });
    ws['!merges'].push({ s: {r:1, c:0}, e: {r:1, c:colNumber-1} });
    ws['!merges'].push({ s: {r:2, c:0}, e: {r:2, c:colNumber-1} });
    ws['!merges'].push({ s: {r:3, c:0}, e: {r:3, c:colNumber-1} });

    XLSX.utils.book_append_sheet(wb, ws, name);
    XLSX.writeFile(wb, name+'.'+type);
}
function generatePDF(dtid){
    var fonts = {
	Roboto: {
		normal: 'fonts/Roboto-Regular.ttf',
		bold: 'fonts/Roboto-Medium.ttf',
		italics: 'fonts/Roboto-Italic.ttf',
		bolditalics: 'fonts/Roboto-MediumItalic.ttf'
	}
};

var data = getData(dtid);
var widths = [];
var l = data[0].length;
for(var i=0;i<l;i++){
    if(i==0 || i==l-1)
    widths.push('auto')
    else
    widths.push('*')
}

var docDefinition = {
	content: [
		{
            text:`{{company('name')}}`, alignment:'left', fontSize: 12, bold: true, lineHeight: 2
        },
		{
            text:`{{__($dt_title)}}`, alignment:'left', fontSize: 11, bold: true
        },
		{
            text:`Tanggal Laporan: {{date('d/m/Y H:i:s')}}`, alignment:'left', fontSize: 10, lineHeight: 2
        },
		{
			style: 'tableExample',
			table: {
                widths: widths,
				headerRows: 1,
				body: data
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
function parseNumber(val){
    if(val=='' || val==null)return 0;
    return parseFloat(val.split('.').join('').split(',').join('.'));
}
</script>
@endpush
