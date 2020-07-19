@php 
$active_menu=''; 
$breadcrumbs = array(
    ['label'=>__('Company'), 'url'=>route('company.profile')],
    ['label'=>__('Convert Abipro Desktop')]
);
@endphp
@extends('layouts.app')
@section('title', __('Convert Abipro Desktop'))
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('company.list_companies')
    </div>
    <div class="col-md-9">
      <div class="card elevation-2">
        <div class="card-header">
        <h3 class="card-title">
        <a href="{{route('companies.index')}}"><i class="fas fa-chevron-left"></i></a> {{__('Convert Abipro Desktop')}} to {{$company->name}}
        
        </h3>
        </div>
        <form id="form" role="form" action="{{route('convert.upload', 'gldept')}}" method="POST" enctype="multipart/form-data" >
          @csrf
          <div class="card-body">
            <div class="form-group">
              <label>Abipro Desktop Folder</label>
                    <input  type="file" class="form-control " webkitdirectory directory multiple id="file" placeholder="" value="">
              
              <small class="text-muted"></small>
            </div>
            Berikut ini file-file yang diperlukan dalam proses konversi ini:
            <table class="table table-sm">
              <thead>
                <tr>
                  <th style="width:30px">No.</th>
                  <th>Nama File</th>
                  <th class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
              @php 
              $filteredFiles=[
          'gltype', 'glnama', 'glmast', 'gldept', 
          'gls1', 'gls2', 'gls3', 'gls4', 'gls5', 'gls6', 
          'gltran', 
        ];
              @endphp
                @foreach($filteredFiles as $i=>$file)
                <tr>
                <td>{{$i+1}}</td>
                <td>{{$file}}.dbf</td>
                <td class="text-center" id="status_{{$file}}"><i class="fas fa-minus text-danger fa-xs"></i></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="card-footer text-right">
            <button id="btn-submit" type="submit" disabled class="btn btn-primary"> {{__('Convert')}}  </button>
          </div>
        </form>
        <div class="overlay loading" style="display:none">
                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
              </div>
      </div>
    </div>
</div>

@endsection

@push('js')
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
<script type="text/javascript">
$(function () {
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': "{{csrf_token()}}"
    }
  });
  bsCustomFileInput.init();
  $('.btn-import').click(function(e){
    $('#import-name').val($(this).attr('data-name'))
    $('#import-title').html($(this).attr('data-title'))
    // $('#modal-import').modal();
  })

  var filteredFiles=[
          'gltype.dbf', 'glnama.dbf', 'glmast.dbf', 'gldept.dbf', 
          'gls1.dbf', 'gls2.dbf', 'gls3.dbf', 'gls4.dbf', 'gls5.dbf', 'gls6.dbf', 
          'gltran.dbf', 
        ];
  var folder = document.getElementById("file");
    var selected = [];
    var form_data = new FormData();  
  folder.onchange=function(){
    $('.loading').show();
    var files = folder.files,
        len = files.length,
        i, exist=0;
    
    for(i=0;i<len;i+=1){
      var file = files[i];
        var filename = file.name.toLowerCase();
      if(filteredFiles.indexOf(filename)>-1){
        filename = filename.split('.')[0];
        $('#status_'+filename).html('<span class=" text-success">exists <i class="fas fa-check fa-xs"></i></span>')
        form_data.append(filename, file);
        exist++;
      }
    }
    if(exist==11){
      $('#btn-submit').prop('disabled', false)
    }
    $('.loading').hide();
  }
  
  $( '#form' ).submit(function ( e ) {
    e.preventDefault();
    var form=$(this).get(0);
    Swal.fire({
      title: '{{__("Warning")}}!',
      icon: 'warning',
      html:'{{__("This process will delete all your data. Are you sure want to continue this process?")}}',
      showCloseButton: false,
      showCancelButton: true,
      focusConfirm: false
    }).then(function(result){
      if(result.value){
        $.ajax({
            url: '{{route("companies.convert.upload")}}',
            data: form_data,
            processData: false,
            contentType:false,
            type: 'POST',
            beforeSend:function(){
              $('.loading').show();
            },
            success: function ( res ) {
              for(var i=0;i<res.length;i++){
                var data = res[i]
                $('#status_'+data.target).html('<span class=" text-success">uploaded <i class="fas fa-check fa-xs"></i></span>')
              }
              convert(0, 10);
            }
        });
      }
    });
  });
  function convert(start, end){
    var id = (filteredFiles[start]).split('.')[0];
    $.ajax({
      url: BASE_URL+'/conversion/execute/'+id,
      type:'POST',
      success:function(res){
        start = start+1;
        if(res.status=='success'){
          $('#status_'+id).html('<span class=" text-success">converted <i class="fas fa-check fa-xs"></i></span>')
          if(start<=end){
            convert(start, end)
          }else{
            $('.loading').hide();
            Swal.fire({
              title: '{{__("Success")}}!',
              text: '{!! __("Abipro Desktop conversion successfully") !!}',
              icon: 'success',
          })
          }
        }else{
          $('#status_'+id).html('<span class=" text-danger">failed <i class="fas fa-check fa-xs"></i></span>')
        }
      }
    })
  }
});
  
</script>
@endpush