@extends('layouts.topnav')
@section('title', 'Abipro Desktop Conversion')  
@section('content')
<div class="card elevation-2">
    <div class="card-header text-center p-2">
    <h3>{{__('Chart of Account Conversion')}}</h3>
    <p class="text-muted">This wizard guides you to convert chart of accounts in Abipro Desktop version to Abipro Web</p>
    </div>
    <div class="card-header text-center p-0">
      <ul class="nav nav-pills">
          <li style="width: 25%;" class="p-2 {{request('step', 1)==1?'bg-purple':''}}">
            1. Convert gltype
          </li>
          <li style="width: 25%;" class="p-2 {{request('step')==2?'bg-purple':''}}">
            2. Account Type Mapping
          </li>
          <li style="width: 25%;" class="p-2 {{request('step')==3?'bg-purple':''}}">
          3. Convert glnama</li>
          <li style="width: 25%;" class="p-2 {{request('step')==4?'bg-purple':''}}">
          4. Convert glmast</li>
      </ul>
    </div>
    @if(request('step', 1)==2)
      @include('convert.account_type_mapping')
    @else
      @include('convert._upload_gltype')
    @endif
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