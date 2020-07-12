@extends('layouts.topnav')
@section('title', 'Abipro Desktop Conversion')  
@section('content')
<div class="row">
  <div class="col-md-3">

  </div>
  <div class="col-md-6">
  <div class="card elevation-2">
    <div class="card-header text-center p-2">
    <h3>{{__('Journal Conversion')}}</h3>
    </div>
    <form action="{{route('convert.upload', 'gltran')}}" method="POST" enctype="multipart/form-data" >
      @csrf
      <div class="card-body">
      <div class="form-group">
      <label>Browse gltran.dbf file</label>
      <div class="input-group">
          <div class="custom-file">
            <input  required type="file" class="form-control custom-file-input @error('file') is-invalid @enderror" name="file" id="file" placeholder="" value="">
            <label class="custom-file-label" for="file">{{__('Browse File')}}</label>
          </div>
      </div>
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
});
</script>
@endpush