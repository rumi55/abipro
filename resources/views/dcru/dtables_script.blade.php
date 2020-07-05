@if(count($filter)>0)
@include('dcru._dtfilter', ['fields'=>$filter['fields']])
@endif
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
    $bactions .= '<form action="'.asset(route('dcru.delete.all', ['name'=>$name], false)).'" method="POST" style="display:inline;">'.csrf_field().'<button type="button" data-confirm="true" data-confirm-text="Apakah Anda yakin ingin menghapus item terpilih?" class="dropdown-item bulk-btn"><i class="fas fa-trash"></i> Hapus</button></form>';
    $cb_actions++;
  }else{
    $routes = explode('.', $act['route']['name']);
    if(has_action($routes[0], $routes[1])){
      $params = isset($act['route']['params'])?$act['route']['params']:[];
      $bactions .= '<form action="'.asset(route($act['route']['name'], $params, false)).'" method="POST" style="display:inline;">'.csrf_field().'<button type="button" data-confirm="'.(isset($act['confirm'])?true:false).'" '.(isset($act['confirm'])?('data-confirm-text="'.$act['confirm'].'"'):'').' class="dropdown-item bulk-btn">'.(isset($act['icon'])?('<i class="'.$act['icon'].'"></i> '):'') .$act['label'].'</button></form>';
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
            className:"text-right",
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
            title:'{{__($dt_title)}}',
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
    @if(count($filter)>0)
    $('#btn-filter-{{$dtname}}').click(function(e){
      load{{$dtname}}();
    });
    @endif
    $('#{{$dtname}}').on( 'draw.dt', function () {
      $('.bulk-actions-wrapper-{{$dtname}}').html(
        '<div class="btn-group">'+
          '<button id="dt-btn-print-{{$dtname}}" type="button" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> {{__("Print")}}</button>'+
          '<button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown">'+
            '<span class="sr-only">Toggle Dropdown</span>'+
            '<div class="dropdown-menu" role="menu">'+
              '<a id="dt-btn-pdf-{{$dtname}}" class="dropdown-item" href="#"><i class="fas fa-file-pdf"></i> PDF</a>'+
              '<a id="dt-btn-excel-{{$dtname}}" class="dropdown-item" href="#"><i class="fas fa-file-excel"></i> Excel</a>'+
              '<a id="dt-btn-csv-{{$dtname}}" class="dropdown-item" href="#"><i class="fas fa-file-csv"></i> CSV</a>'+
              '<a id="dt-btn-copy-{{$dtname}}" class="dropdown-item" href="#"><i class="fas fa-copy"></i> Copy</a>'+
            '</div>'+
          '</button>'+
        '</div>'
      );
      $('#dt-btn-pdf-{{$dtname}}').click(function(){$('.buttons-pdf[aria-controls={{$dtname}}]').trigger('click')})
      $('#dt-btn-excel-{{$dtname}}').click(function(){$('.buttons-excel[aria-controls={{$dtname}}]').trigger('click')})
      $('#dt-btn-print-{{$dtname}}').click(function(){$('.buttons-print[aria-controls={{$dtname}}]').trigger('click')})
      $('#dt-btn-copy-{{$dtname}}').click(function(){$('.buttons-copy[aria-controls={{$dtname}}]').trigger('click')})
      $('#dt-btn-csv-{{$dtname}}').click(function(){$('.buttons-csv[aria-controls={{$dtname}}]').trigger('click')})
      
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
      
      @if(count($filter)>0)
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
      @endif
      
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
            title: 'Konfirmasi',
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
                $('#id-'+id).remove();
            }   
          });
          if(this.checked){
            $('#bulk-actions-{{$dtname}}').show();
          }else{
            $('#bulk-actions-{{$dtname}}').hide();
          }
    });
  });
  function load{{$dtname}}(){
    @if(count($filter)>0)
    {{$dtname}}config.ajax.data.filter = [
        @foreach($filter['fields'] as $field)
        {name: "{{$field['name']}}", type: "{{$field['type']}}", value: @if($field['type']=='daterange'){start: $("#ft_{{$dtname}}_{{$field['name']}}_start").val(), end: $("#ft_{{$dtname}}_{{$field['name']}}_end").val()} @else  $("#ft_{{$dtname}}_{{$field['name']}}").val() @endif},
        @endforeach
    ];
    @endif
    $('#{{$dtname}}').DataTable().destroy();
    $('#{{$dtname}}').DataTable({{$dtname}}config);
  }
</script>
@endpush