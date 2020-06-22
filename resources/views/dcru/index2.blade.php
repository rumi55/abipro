@extends('layouts.app')
@section('title', $title)
@section('content')
<div class="row">
  <div class="col-lg-12">
    @component('components.card')
      @slot('title') {{$section_title}} @endslot
      @slot('tools')
        <button type="button" class="btn btn-tool" data-toggle="dropdown">
          <i class="fas fa-th"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right" role="menu">
          @if(has_action($name.'-'.'create'))
          <a href="{{asset(route('dcru.create',['name'=>$name], false))}}" class="dropdown-item" ><i class="fas fa-plus"></i> Tambah Baru</a>
          @endif
          <a class="dropdown-divider"></a>
          <!-- <a id="btn-pdf" href="#" class="dropdown-item"><i class="fas fa-file-pdf"></i> PDF</a>
          <a id="btn-excel" href="#" class="dropdown-item"><i class="fas fa-file-excel"></i> Excel</a>
          <a id="btn-csv" href="#" class="dropdown-item"><i class="fas fa-file-csv"></i> CSV</a>
          <a id="btn-copy" href="#" class="dropdown-item"><i class="fas fa-copy"></i> Salin</a> -->
          <a id="btn-print" href="#" class="dropdown-item"><i class="fas fa-print"></i> Cetak</a>
        </div>
      @endslot
      <div class="table-responsive-sm">
          <table id="data-table" style="width:100%" class="table table-hover">
              <thead>
                  <tr>
                      @foreach($columns as $column)
                      @if($column['type']=='checkbox')
                      <th scope="col" class="noexport"><input type="checkbox" class="check-all"/></th>
                      @elseif($column['type']=='menu')
                      <th scope="col" class="noexport"></th>  
                      @elseif(!(isset($column['visible']) && $column['visible']==false))
                        <th scope="col" >{{$column['title']}}</th>
                      @endif
                      @endforeach
                  </tr>
              </thead>
          </table>
      </div>
    @endcomponent
  </div>
</div>
@if(count($actions)>0)
@component('components.modal_form', [
'id'=>'modal-delete-all', 'title'=>'Hapus', 'btn_label'=>'Hapus','bg'=>'bg-danger',
'method'=>'delete', 'action'=>asset(route('dcru.delete.all', ['name'=>$name], false))
])
  Apakah Anda yakin akan menghapus semua data terpilih?
  <div id="selected-id">
  
  </div>
@endcomponent
@component('components.modal_form', [
'id'=>'modal-delete', 'title'=>'Hapus', 'btn_label'=>'Hapus','bg'=>'bg-danger',
'method'=>'delete', 'action'=>asset(route('dcru.delete', ['name'=>$name, 'id'=>'0'], false)), 'form_id'=>'form-delete'
])
  Apakah Anda yakin akan menghapus data terpilih ini?
@endcomponent
@component('components.modal_form', [
'id'=>'modal-delete-permanent', 'title'=>'Hapus Permanen', 'btn_label'=>'Hapus','bg'=>'bg-danger',
'method'=>'delete', 'action'=>asset(route('dcru.delete.permanent', ['name'=>$name, 'id'=>'0'], false)), 'form_id'=>'form-delete-permanent'
])
  Apakah Anda yakin akan menghapus permanen data terpilih ini?
@endcomponent
@endif

@endsection

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
          { 
            data:"{{$dt['data']}}",
            name:"{{$dt['name']}}",
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
        @endforeach
        
      ],
      dom:"<'row mb-2'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6 text-right hide'B>>"+
        "<'row'<'col-sm-12 col-md-6 delete-all-wrapper'><'col-sm-12 col-md-6'f>>"+
        "<'row'<'col-sm-12'tr>>"+
        "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4 text-center'i><'col-sm-12 col-md-4'p>>",
      buttons: [
        {
            extend: 'copyHtml5',
            title:'{{$section_title}}',
            exportOptions: {
              columns: ':visible:not(.noexport)'
            }
        },
        {
            extend: 'csvHtml5',
            title:'{{$section_title}}',
            exportOptions: {
              columns: ':visible:not(.noexport)'
            }
        },
        {
            extend: 'excelHtml5',
            title:'{{$section_title}}',
            exportOptions: {
              columns: ':visible:not(.noexport)'
            }
        },
        {
            extend: 'pdfHtml5',
            title:'{{$section_title}}',
            exportOptions: {
              columns: ':visible:not(.noexport)'
            }
        },
        {
            extend: 'print',
            title:'{{$section_title}}',
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
    $('#btn-pdf').click(function(){$('.buttons-pdf').trigger('click')})
    $('#btn-excel').click(function(){$('.buttons-excel').trigger('click')})
    $('#btn-print').click(function(){$('.buttons-print').trigger('click')})
    $('#btn-copy').click(function(){$('.buttons-copy').trigger('click')})
    $('#btn-csv').click(function(){$('.buttons-csv').trigger('click')})
    $('#data-table').on( 'draw.dt', function () {
      $('.delete-all-wrapper').html('<button data-toggle="modal" data-target="#modal-delete-all" class="btn btn-sm btn-danger hide" id="delete-all" ><i class="fas fa-trash"></i> Hapus</button>');
      $(".check-row" ).change( function(){
        var id = $(this).val();
        if(this.checked){
            $('#selected-id').append('<input id="id-'+id+'" name="id[]" type="hidden" value="'+id+'" >')
        }else{
            $('#id-'+id).remove();
        }
        var check = 0;
          $( ".check-row:checked" ).each(function(){
            check++;
          });
          if(check>0){
            $('#delete-all').show();
          }else{
            $('#delete-all').hide();
          }
      });
      $('.btn-delete').click(function(e){
        var form = $(this).parent();
        Swal.fire({
          title: 'Hapus',
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
                $('#selected-id').append('<input id="id-'+id+'" name="id[]" type="hidden" value="'+id+'" >')
            }else{
                $('#id-'+id).remove();
            }   
          });
          if(this.checked){
            $('#delete-all').show();
          }else{
            $('#delete-all').hide();
          }
    });
  });
</script>
@endpush