@foreach($fields as $field)
@if(!($mode=='edit' && isset($field['edit']) && $field['edit']==false))
<div class="form-group row">
    @php 
        $name = $field['name'];
        $label = isset($field['label'])?$field['label']:'';
        $type = $field['type']; 
        if(isset($field['svalidation']) && $mode=='create'){
            $required = (strpos($field['svalidation'], 'required')!==false);
        }else{
            $required = false;
        }
        if(isset($field['uvalidation']) && $mode=='edit'){
            $required = (strpos($field['uvalidation'], 'required')!==false);
        }else{
            $required = false;
        }
        
        $col = isset($field['width'])?$field['width']:'9';
    @endphp
    @if($type=='hidden' || $type=='generate')
        @php $val = isset($field['value'])?dcru_hiddenval($field['value']):'' @endphp
        <input type="hidden" name="{{$name}}" id="{{$name}}" value="{{$val}}" >
    @else
        <label for="{{$name}}" class="col-sm-2 col-form-label">
        {{$label}}
        @if($required)
        <sup>*</sup>
        @endif
        </label>
        <div class="col-md-{{$col}} col-sm-10">
        @switch($type)
            @case('hidden')
                @break
            @case('file')
                @if($mode=='edit' && !empty($data->$name))
                    <div class="col-md-3 col-sm-6">
                        <a target="_blank" style="width:100%;margin:0;" class="btn btn-app" href="{{asset(url_file($data->$name))}}"  ><i class="fas fa-file"></i> File</a>
                            <button type="button" class="btn btn-block btn-danger btn-sm btn-delete" data-file="{{$name}}" ><i class="fas fa-trash"></i> Hapus</button>
                            <small class="form-text text-mute">Untuk mengubah file, hapus terlebih dahulu file di atas.</small>
                            <input type="hidden" name="{{'_'.$name.'_'}}" id="{{$name}}" value="{{old($name, isset($data->$name)?$data->$name:'')}}">
                        </a>
                    </div>
                @else
                    <div class="input-group">
                        <div class="custom-file">
                            <input  @if($required) required @endif type="file" class="form-control custom-file-input @error($name) is-invalid @enderror" name="{{$name}}" id="{{$name}}" placeholder="{{$label}}" value="{{old($name, isset($data->$name)?$data->$name:'')}}">
                            <label class="custom-file-label" for="{{$name}}">Pilih File</label>
                        </div>
                    </div>
                @endif
                @break
            @case('image')
                @if($mode=='edit' && !empty($data->$name))
                    <div class="col-md-3 col-sm-6">
                        <a target="_blank" href="{{asset(url_file($data->$name))}}"  ><img src="{{asset(url_file($data->$name))}}" class="img-thumbnail" ></a>
                        <button type="button" class="btn btn-block btn-danger btn-sm btn-delete" data-file="{{$name}}" ><i class="fas fa-trash"></i> Hapus</button>
                        <small class="form-text text-mute">Untuk mengubah gambar, hapus terlebih dahulu gambar di atas.</small>
                        <input type="hidden" name="{{'_'.$name.'_'}}" id="{{$name}}" value="{{old($name, isset($data->$name)?$data->$name:'')}}">
                    </div>
                @else
                    <div class="input-group">
                        <div class="custom-file">
                            <input  @if($required) required @endif type="file" class="form-control custom-file-input @error($name) is-invalid @enderror" name="{{$name}}" id="{{$name}}" placeholder="{{$label}}" value="{{old($name, isset($data->$name)?$data->$name:'')}}">
                            <label class="custom-file-label" for="{{$name}}">Pilih File</label>
                        </div>
                    </div>
                @endif
                @break
            @case('select')
                <select @if($required) required @endif class="form-control select2bs4 @error($name) is-invalid @enderror" name="{{$name}}" id="{{$name}}">
                    <option value="">--Silakan Pilih--</option>
                    @if($field['options']['type']=='query')
                        @php $opts = dcru_query($field['options']['query'])->get(); @endphp
                        @foreach($opts as $opt)
                        <option {{ ($opt->val ==  old($name, isset($data->$name)?$data->$name:''))?'selected':''}} value="{{$opt->val}}">{{$opt->txt}}</option>
                        @endforeach
                    @elseif($field['options']['type']=='array')
                        @foreach($field['options']['items'] as $opt)
                        <option {{ ($opt['value'] ==  old($name, isset($data->$name)?$data->$name:''))?'selected':''}} value="{{$opt['value']}}">{{$opt['text']}}</option>
                        @endforeach
                    @endif
                </select>
                @break
            @case('multiselect')
                @php 
                    $values = array();
                    $old = old($name, isset($data->$name)?$data->$name:'');
                    $values = explode(',',$old);
                @endphp
                
                <select @if($required) required @endif class="form-control select2bs4 @error($name) is-invalid @enderror" name="{{$name}}[]" id="{{$name}}" multiple="multiple">
                    @if($field['options']['type']=='query')
                        @php $opts = dcru_query($field['options']['query'])->get(); @endphp
                        @foreach($opts as $opt)
                        <option {{ in_array($opt->val, $values, TRUE)?'selected':''}} value="{{$opt->val}}">{{$opt->txt}}</option>
                        @endforeach
                    @elseif($field['options']['type']=='array')
                        @php 
                        $opts = $field['options']['items'];
                        @endphp
                        @foreach($opts as $opt)
                        <option {{ in_array($opt['value'], $values, TRUE)?'selected':''}} value="{{$opt['value']}}">{{$opt['text']}}</option>
                        @endforeach
                    @endif
                </select>
                @break
            @case('editor')
                <textarea rows="30" @if($required) required @endif class="form-control editor @error($name) is-invalid @enderror" name="{{$name}}" id="{{$name}}" placeholder="{{$label}}" >{{old($name, isset($data->$name)?$data->$name:'')}}</textarea>
                @break
            @case('ltext')
                <textarea rows="5" @if($required) required @endif class="form-control @error($name) is-invalid @enderror" name="{{$name}}" id="{{$name}}" placeholder="{{$label}}" >{{old($name, isset($data->$name)?$data->$name:'')}}</textarea>
                @break
            @case('boolean')
                <input @if($required) required @endif type="checkbox" value="1" class="@error($name) is-invalid @enderror" name="{{$name}}" id="{{$name}}" {{old($name, isset($data->$name)?$data->$name:'')=='1'?'checked':''}}  >
                @break
            @case('date')
                <input @if($required) required @endif type="text" class="form-control datemask @error($name) is-invalid @enderror" name="{{$name}}" id="{{$name}}" value="{{fdate(old($name, isset($data->$name)?$data->$name:''))}}"  data-inputmask-alias="datetime" data-inputmask-inputformat="dd-mm-yyyy" data-mask>
                @break
            @case('daterangetime')
                  <input @if($required) required @endif type="text" class="form-control daterangetime @error($name) is-invalid @enderror" name="{{$name}}" id="{{$name}}" value="{{fdate(old($name, isset($data->$name)?$data->$name:''))}}">
                @break
            @case('datetime')
                  <input @if($required) required @endif type="text" class="form-control datetime @error($name) is-invalid @enderror" name="{{$name}}" id="{{$name}}" value="{{fdate(old($name, isset($data->$name)?$data->$name:''))}}">
                @break
            @case('password')
                <input @if($required) required @endif type="password" class="form-control @error($name) is-invalid @enderror" name="{{$name}}" id="{{$name}}" placeholder="{{$label}}" value="{{old($name, isset($data->$name)?$data->$name:'')}}">
                @break
            @case('email')
                <input @if($required) required @endif type="email" class="form-control @error($name) is-invalid @enderror" name="{{$name}}" id="{{$name}}" placeholder="{{$label}}" value="{{old($name, isset($data->$name)?$data->$name:'')}}">
                @break
            @default
                <input @if($required) required @endif type="text" class="form-control @error($name) is-invalid @enderror" name="{{$name}}" id="{{$name}}" placeholder="{{$label}}" value="{{old($name, isset($data->$name)?$data->$name:'')}}">
        @endswitch
        
        @isset($field['helper'])
        <small class="form-text text-mute">{!!$field['helper']!!}</small>
        @endisset
        @error($name)
        <small class="form-text text-danger">{!!$message!!}</small>
        @enderror
        </div>
    @endif
</div>
@endif
@endforeach

@push('css')
  <link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/summernote/summernote-bs4.css')}}">
@endpush

@push('js')
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js')}}"></script>
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/inputmask/min/jquery.inputmask.bundle.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
<script src="{{asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
<script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>

<script type="text/javascript">
$(function () {
  $('.btn-delete').click(function(e){
    e.preventDefault();
    var file = $(this).attr('data-file');
    $('#file').val(file);
    $('#modal-delete').modal('show');
  });
});
$(function () {
    bsCustomFileInput.init();
    $('.select2').select2();
    $('.editor').summernote()   
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    });
    $('.datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
    //Datemask2 mm/dd/yyyy
    $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
    //Money Euro
    $('[data-mask]').inputmask()

    //Date range picker
    $('.daterange').daterangepicker()
    //Date range picker with time picker
    $('.daterangetime').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      timePicker24Hour: true,
      autoApply:true,
      locale: {
        format: 'DD-MM-YYYY HH:mm'
      }
    });
    $('.datetime').daterangepicker({
      timePicker: true,
      singleDatePicker:true,
      timePickerIncrement: 30,
      timePicker24Hour: true,
      autoApply:true,
      locale: {
        format: 'DD-MM-YYYY HH:mm'
      }
    });
    
    $('.time').datetimepicker({
      format: 'LT'
    })
    $('.duallistbox').bootstrapDualListbox()
    $('.color').colorpicker()
    $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });

  })
</script>
@endpush