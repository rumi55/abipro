<div class="table-responsive-sm">
  <table id="data-table" style="width:100%" class="table table-hover">
      <thead>
          <tr>
              @foreach($columns as $column)
                @if(!($column['type']=='checkbox' && count($bulk_actions)==0))
                  @if($column['type']=='checkbox')
                  <th style="width:20px" scope="col" class="noexport"><input type="checkbox" class="check-all"/></th>
                  @elseif($column['type']=='menu')
                  <th style="width:20px" scope="col" class="noexport"></th>  
                  @elseif(!(isset($column['visible']) && $column['visible']==false))
                    <th scope="col" >{{$column['title']}}</th>
                  @endif
                @endif
              @endforeach
          </tr>
      </thead>
  </table>
</div>

@push('css')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
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
</style>
@endpush
<?php 
$cb_actions = 0;
$bactions = '';
foreach($bulk_actions as $act){
  if(has_action($act['route']['name'])){
    if(isset($act['type']) && $act['type']=='delete'){
      $bactions .= '<form action="'.asset(route('dcru.delete.all', ['name'=>$name], false)).'" method="POST" style="display:inline;">'.csrf_field().'<button type="button" data-confirm="true" data-confirm-text="Apakah Anda yakin ingin menghapus item terpilih?" class="dropdown-item bulk-btn"><i class="fas fa-trash"></i> Hapus</button></form>';
    }else{
      $params = isset($act['route']['params'])?$act['route']['params']:[];
      $bactions .= '<form action="'.asset(route($act['route']['name'], $params, false)).'" method="POST" style="display:inline;">'.csrf_field().'<button type="button" data-confirm="'.(isset($act['confirm'])?true:false).'" '.(isset($act['confirm'])?('data-confirm-text="'.$act['confirm'].'"'):'').' class="dropdown-item bulk-btn">'.(isset($act['icon'])?('<i class="'.$act['icon'].'"></i> '):'') .$act['label'].'</button></form>';
    }
    $cb_actions++;
  }
}
?>


@push('js')
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.print.min.js"></script>
<script>
  $(function () {
    var dtconfig = {
      processing: true,
      serverSide: true,
      ajax: {
        url: "{!! asset(route('dcru.dt', ['name'=>$name], false)) !!}"
      },
      lengthMenu: [[10, 25, 50, 100,-1], [10, 25, 50, 100,"Semua"]],
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
            title:"{{$dt['title']}}",
            @endif
            @if($dt['type']=='checkbox'||$dt['type']=='menu')
            searchable:false,
            orderable:false,
            @else
            searchable:{{$dt['searchable']==1?'true':'false'}},
            orderable:{{$dt['orderable']==1?'true':'false'}},
            visible:{{$dt['visible']==1?'true':'false'}}            
            @endif
          },
          @endif
        @endforeach
      ],
      dom:"<'row mb-2'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6 text-right hide'B>>"+
        "<'row'<'col-sm-12 col-md-6 bulk-actions-wrapper'><'col-sm-12 col-md-6'f>>"+
        "<'row'<'col-sm-12'tr>>"+
        "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4 text-center'i><'col-sm-12 col-md-4'p>>",
      buttons: [
        {
            extend: 'copyHtml5',
            title:'{{$dt_title}}',
            exportOptions: {
              columns: ':visible:not(.noexport)'
            }
        },
        {
            extend: 'csvHtml5',
            title:'{{$dt_title}}',
            exportOptions: {
              columns: ':visible:not(.noexport)'
            }
        },
        {
            extend: 'excelHtml5',
            title:'{{$dt_title}}',
            exportOptions: {
              columns: ':visible:not(.noexport)'
            }
        },
        {
            extend: 'pdfHtml5',
            title:'{{$dt_title}}',
            exportOptions: {
              columns: ':visible:not(.noexport)'
            }
        },
        {
            extend: 'print',
            title:'{{$dt_title}}',
            exportOptions: {
              columns: ':visible:not(.noexport)'
            }
        },
    ],
      language: {
          "emptyTable": "Tidak ada data",
          "info": "_START_ - _END_ dari _TOTAL_ baris",
          "infoEmpty": "",
          "lengthMenu":   "_MENU_ baris",
          "search": "Cari:",
          "zeroRecords": "Tidak ditemukan data yang sesuai",
          "paginate": {
            "first": "<",
            "last": ">",
            "next": ">>",
            "previous": "<<"
          }
        },
    };
    $('#data-table').DataTable(dtconfig);
    
    $('#data-table').on( 'draw.dt', function () {
      $('.bulk-actions-wrapper').html(
        '<div class="btn-group">'+
          '<button id="dt-btn-print" type="button" class="btn btn-info btn-sm"><i class="fas fa-print"></i> Cetak</button>'+
          '<button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">'+
            '<span class="sr-only">Toggle Dropdown</span>'+
            '<div class="dropdown-menu" role="menu">'+
              '<a id="dt-btn-pdf" class="dropdown-item" href="#"><i class="fas fa-file-pdf"></i> PDF</a>'+
              '<a id="dt-btn-excel" class="dropdown-item" href="#"><i class="fas fa-file-excel"></i> Excel</a>'+
              '<a id="dt-btn-csv" class="dropdown-item" href="#"><i class="fas fa-file-csv"></i> CSV</a>'+
              '<a id="dt-btn-copy" class="dropdown-item" href="#"><i class="fas fa-copy"></i> Copy</a>'+
            '</div>'+
          '</button>'+
        '</div>'
      );
      $('#dt-btn-pdf').click(function(){$('.buttons-pdf').trigger('click')})
      $('#dt-btn-excel').click(function(){$('.buttons-excel').trigger('click')})
      $('#dt-btn-print').click(function(){$('.buttons-print').trigger('click')})
      $('#dt-btn-copy').click(function(){$('.buttons-copy').trigger('click')})
      $('#dt-btn-csv').click(function(){$('.buttons-csv').trigger('click')})
      @if($cb_actions>0)
      $('.bulk-actions-wrapper').append(
        '<div id="dt-selected-id" style="display:inline"></div>'+
        '<div class="dropdown" style="display:inline;margin-left:5px">'+
        '<button id="bulk-actions" style="display:none" type="button" class="btn btn-sm btn-danger dropdown-toggle" data-toggle="dropdown">Tindakan Massal</button>'+        
        '<div class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="bulk-actions">'+
        '{!! $bactions !!}'+
        '</div>'+
        '</div>'
      );
      @endif
      $(".check-row" ).change( function(){
        var id = $(this).val();
        if(this.checked){
            $('#dt-selected-id').append('<input id="id-'+id+'" name="id[]" type="hidden" value="'+id+'" >')
        }else{
            $('#id-'+id).remove();
        }
        var check = 0;
          $( ".check-row:checked" ).each(function(){
            check++;
          });
          if(check>0){
            $('#bulk-actions').show();
          }else{
            $('#bulk-actions').hide();
          }
      });
      $('.btn-delete').click(function(e){
        var form = $(this).parent();
        Swal.fire({
          title: 'Peringatan!',
          icon: 'warning',
          html:'Apakah Anda yakin ingin menghapus item tersebut?',
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
              var id = $('#dt-selected-id').html();
              form.append(id);
              form.submit();
            }
          })
        }else{
          form.submit();
        }
        
      });
      @endif
      $('.btn-delete-permanent').click(function(e){
        var url = $(this).attr('data-url');
        $('#form-delete-permanent').attr('action', url);
      });

    } );
    $('.check-all').change(function() {
          $( ".check-row" ).prop( "checked", this.checked );
          $( ".check-row" ).each(function(){
            var id = $(this).val();
            if(this.checked){
                $('#dt-selected-id').append('<input id="id-'+id+'" name="id[]" type="hidden" value="'+id+'" >')
            }else{
                $('#id-'+id).remove();
            }   
          });
          if(this.checked){
            $('#bulk-actions').show();
          }else{
            $('#bulk-actions').hide();
          }
    });



  });
</script>
@endpush