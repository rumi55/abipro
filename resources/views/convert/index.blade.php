@extends('layouts.topnav')
@section('title', 'Abipro Desktop Conversion')  
@section('content')
<div class="row d-flex align-items-stretch">
  <div class="col-12 col-sm-12 col-md-3 d-flex align-items-stretch">
    <div class="card elevation-3"style="width:100%">
        <div class="card-body">
          <h3>Department Data Conversion</h3>
          <p class="text-muted">This process will convert <b>gldept.dbf</b> file of Abipro Desktop Version to your company database.</p>
        </div>
        <div class="card-footer">
          <a href="{{route('convert.departments')}}" class="btn btn-lg btn-block btn-primary">Covert</a>
        </div>
    </div>
  </div>
  <div class="col-12 col-sm-12 col-md-3 d-flex align-items-stretch">
    <div class="card elevation-3"style="width:100%">
        <div class="card-body">
          <h3>Sortir Data Conversion</h3>
          <p class="text-muted">This process will convert <b>gls1.dbf, gls2.dbf, gls3.dbf, gls4.dbf, gls5.dbf, and gls6.dbf</b> file of Abipro Desktop Version to your company database.</p>
        </div>
        <div class="card-footer">
          <a href="{{route('convert.sortirs')}}" class="btn btn-lg btn-block btn-primary">Covert</a>
        </div>
    </div>
  </div>
  <div class="col-12 col-sm-12 col-md-3 d-flex align-items-stretch">
    <div class="card elevation-3"style="width:100%">
        <div class="card-body">
          <h3>Chart of Account Conversion</h3>
          <p class="text-muted">This process will convert <b>gltype.dbf, glnama.dbf, and glmast.dbf</b> file of Abipro Desktop Version to your company database.</p>
        </div>
        <div class="card-footer">
          <a href="{{route('convert.accounts')}}" class="btn btn-lg btn-block btn-primary">Covert</a>
        </div>
    </div>
  </div>
  <div class="col-12 col-sm-12 col-md-3 d-flex align-items-stretch">
    <div class="card elevation-3"style="width:100%">
        <div class="card-body">
          <h3>Journal Conversion</h3>
          <p class="text-muted">This process will convert <b>gltran.dbf</b> file of Abipro Desktop Version to your company database.</p>
        </div>
        <div class="card-footer">
          <a href="{{route('convert.journals')}}" class="btn btn-lg btn-block btn-primary">Covert</a>
        </div>
    </div>
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