@extends('layouts.topnav')
@section('title', 'Abipro Desktop Conversion')  
@section('content')
<div class="row">
  <div class="col-md-3">

  </div>
  <div class="col-md-6">
  <div class="card elevation-2">
    <div class="card-header text-center p-2">
    <h3>{{__('Department Conversion')}}</h3>
    </div>
    <form id="form" role="form" action="{{route('convert.upload', 'gldept')}}" method="POST" enctype="multipart/form-data" >
      @csrf
      <div class="card-body">
        <div class="form-group">
          <label>Browse gldept.dbf file</label>
          <div class="input-group">
              <div class="custom-file">
                <input  type="file" class="form-control custom-file-input @error('file') is-invalid @enderror" name="file" id="file" placeholder="" value="">
                <label class="custom-file-label" for="file">{{__('Browse File')}}</label>
              </div>
          </div>
          <small class="text-muted"></small>
        </div>
        <div class="form-group">
          <label>Browse gldept.dbf file</label>
          
                <input id="myInput" type="file" webkitdirectory directory multiple>
          <small class="text-muted"></small>
        </div>
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary">{{__('Process')}} <i class="fas fa-arrow-right"></i> </button>
      </div>
    </form>
  </div>
  </div>
  <div class="col-md-3">

  </div>
</div>

@endsection

@push('js')
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
<script type="text/javascript">
$(function () {
  bsCustomFileInput.init();
  $('.btn-import').click(function(e){
    $('#import-name').val($(this).attr('data-name'))
    $('#import-title').html($(this).attr('data-title'))
    // $('#modal-import').modal();
  })


  var folder = document.getElementById("myInput");
    var selected = [];
    var form_data = new FormData();  
  folder.onchange=function(){
    var files = folder.files,
        len = files.length,
        i, filteredFiles=[
          'gltye', 'glnama.dbf', 'glmast.dbf', 'gldept.dbf', 
          'gls1.dbf', 'gls2.dbf', 'gls3.dbf', 'gls4.dbf', 'gls5.dbf', 'gls6.dbf', 
          'gltran.dbf', 
        ];
    for(i=0;i<len;i+=1){
      var file = files[i];
      if(filteredFiles.indexOf(file.name.toLowerCase())>-1){
        var filename = file.name.toLowerCase().split('.')[0];
        form_data.append(filename, file);
        // selected.push(file);
      }
    }
    // console.log(form_data);
  }
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': "{{csrf_token()}}"
    }
});

  $( '#form' ).submit(function ( e ) {
    e.preventDefault();
    var form=$(this).get(0);
    $.ajax({
        url: '{{route("convert.upload", "gltype")}}',
        data: form_data,
        processData: false,
        contentType:false,
        type: 'POST',
        success: function ( res ) {
            alert( res );
        }
    });

});
});
</script>
@endpush