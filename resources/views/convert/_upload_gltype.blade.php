@php 
$step = request('step',1);
$gl = $step==1?'gltype':($step==3?'glnama':($step==4?'glmast':''));
@endphp
<form action="{{route('convert.upload', $gl)}}" method="POST" enctype="multipart/form-data" >
  @csrf
  <div class="card-body">
  <div class="form-group">
  <p>This process will replace all of your existing chart of accounts with the conversion results.</p>
  <label>Browse {{$gl}}.dbf file</label>
  <div class="input-group">
      <div class="custom-file">
        <input name="name" id="import-name" type="hidden" />
        <input  required type="file" class="form-control custom-file-input @error('file') is-invalid @enderror" name="file" id="file" placeholder="" value="">
        <label class="custom-file-label" for="file">{{__('Browse File')}}</label>
      </div>
  </div>
  <small class="text-muted"></small>
  </div>
  </div>
  <div class="card-footer">
    <div class="row">
      <div class="col-sm-6">
      @if($step>1)
        <a href="{{route('convert.accounts', ['step'=>$step-1])}}" class="btn btn-primary"><i class="fas fa-arrow-left"></i> {{__('Back')}} </a>
      @endif
      </div>
      <div class="col-sm-6 text-right">
        <button type="submit" class="btn btn-primary">{{__('Process')}} <i class="fas fa-arrow-right"></i> </button>
      </div>
    </div>
  </div>
</form>