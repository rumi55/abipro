<form role="form">
  <div id="filter-{{$dtname}}" class="row collapse {{request('filter')}}">
        @foreach($fields as $field)
          <div class="col">
            <div class="form-group">
                  @php 
                      $name = $field['name'];
                      $label = isset($field['label'])?$field['label']:'';
                      $type = $field['type']; 
                  @endphp
                  <label for="ft_{{$dtname}}_{{$name}}">
                  {{__($label)}}
                  </label>
                  @switch($type)
                      @case('select')
                          <select  class=" form-control select2bs4 " name="ft_{{$dtname}}_{{$name}}" id="ft_{{$dtname}}_{{$name}}">
                              <option value="">--{{__('Please Choose')}}--</option>
                              @if($field['options']['type']=='query')
                                  @php $opts = dcru_query($field['options']['query'])->get(); @endphp
                                  @foreach($opts as $opt)
                                  <option {{ ($opt->val ==  request('ft_'.$dtname.'_'.$name))?'selected':''}} value="{{$opt->val}}">{{$opt->txt}}</option>
                                  @endforeach
                              @elseif($field['options']['type']=='array')
                                  @foreach($field['options']['items'] as $opt)
                                  <option {{ ($opt['value'] ==  request('ft_'.$dtname.'_'.$name))?'selected':''}} value="{{$opt['value']}}">{{$opt['text']}}</option>
                                  @endforeach
                              @endif
                          </select>
                          @break
                      @case('multiselect')
                          @php 
                              $values = array();
                              $old = request('ft_'.$dtname.'_'.$name);
                              $values = explode(',',$old);
                          @endphp
                          
                          <select  class="form-control select2bs4 " name="ft_{{$dtname}}_{{$name}}[]" id="ft_{{$dtname}}_{{$name}}" multiple="multiple">
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
                      @case('boolean')
                          <input autocomplete="off" type="checkbox" value="1" class="" name="ft_{{$dtname}}_{{$name}}" id="ft_{{$dtname}}_{{$name}}" {{request('ft_'.$dtname.'_'.$name)=='1'?'checked':''}}  >
                          @break
                      @case('date')
                          <input autocomplete="off" type="text" class=" form-control datemask " name="ft_{{$dtname}}_{{$name}}" id="ft_{{$dtname}}_{{$name}}" value="{{fdate(request('ft_'.$dtname.'_'.$name))}}"  data-inputmask-alias="datetime" data-inputmask-inputformat="dd-mm-yyyy" data-mask>
                          @break
                      @case('daterangetime')
                            <input autocomplete="off" type="text" class=" form-control daterangetime " name="ft_{{$dtname}}_{{$name}}" id="ft_{{$dtname}}_{{$name}}" value="{{fdate(request('ft_'.$dtname.'_'.$name))}}">
                          @break
                      @case('datetime')
                            <input autocomplete="off" type="text" class=" form-control datetime " name="ft_{{$dtname}}_{{$name}}" id="ft_{{$dtname}}_{{$name}}" value="{{fdate(request('ft_'.$dtname.'_'.$name))}}">
                          @break
                      @case('daterange')
                      <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                        </div>
                        <input style="width:100px;" autocomplete="off" type="text" class=" form-control date" name="ft_{{$dtname}}_{{$name}}_start" id="ft_{{$dtname}}_{{$name}}_start" value="{{fdate(request('ft_'.$dtname.'_'.$name.'_start'))}}">
                        <div class="input-group-prepend">
                          <span class="input-group-text">-</span>
                        </div>
                        <input style="width:100px;" autocomplete="off" type="text" class=" form-control date" name="ft_{{$dtname}}_{{$name}}_end" id="ft_{{$dtname}}_{{$name}}_end" value="{{fdate(request('ft_'.$dtname.'_'.$name.'_end'))}}">
                      </div>
                            
                          @break
                      @default
                          <input autocomplete="off" type="text" class=" form-control " name="ft_{{$dtname}}_{{$name}}" id="ft_{{$dtname}}_{{$name}}" placeholder="{{$label}}" value="{{request('ft_'.$dtname.'_'.$name)}}">
                  @endswitch
                  @isset($field['helper'])
                  <small class="form-text text-mute">{!!$field['helper']!!}</small>
                  @endisset
            </div>
          </div>
        @endforeach
        <div class="col">
          <div class="form-group">
            <label>&nbsp;</label>
            <div>
              <button type="button" id="btn-filter-{{$dtname}}" class="btn btn-info btn-filter"> {{__('Filter')}}</button>
              <button type="button" class="btn btn-default"  data-toggle="collapse" data-target="#filter-{{$dtname}}" aria-expanded="false" aria-controls="filter"  > {{__('Cancel')}}</button>
            </div>
          </div>
        </div>
  </div>
</form>

@push('css')
  <link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush

@push('js')
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js')}}"></script>
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/inputmask/min/jquery.inputmask.bundle.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>

<script type="text/javascript">
$(function () {
    $('.select2').select2();
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    });
    $('.date').daterangepicker({
      singleDatePicker:true,
      showDropdowns:true,
      locale: {
        format: 'DD-MM-YYYY'
      }
    })
  })
</script>
@endpush