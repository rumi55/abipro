<form role="form"  id="filter-{{$dtname}}" class="collapse {{request('filter')}}">
  <div class="row">
        @foreach($fields as $field)
        @php 
        if(in_array($field['type'], ['menu','checkbox', 'icon', 'image']) || (isset($field['filter'])&&$field['filter']==false)){continue;} 
        @endphp
          <div class="col-md-4">
            <div class="form-group">
                  @php 
                      $name = $field['name'];
                      $label = isset($field['title'])?$field['title']:'';
                      $type = $field['type']; 
                      if(isset($field['filter']) && $field['filter']!=false){
                        $type = $field['filter']['type'];
                      }
                  @endphp
                  <label for="ft_{{$dtname}}_{{$name}}">
                  {{__($label)}}
                  </label>
                  @switch($type)
                      @case('select')
                          <select  class=" form-control select2bs4 " name="{{$name}}" id="ft_{{$dtname}}_{{$name}}">
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
                      @case('badge')
                          <select  class="form-control select2bs4 " name="{{$name}}[]" id="ft_{{$dtname}}_{{$name}}" multiple="multiple">
                              @foreach($field['badge'] as $value=>$badge)
                              <option value="{{$value}}">{{$badge['text']}}</option>
                              @endforeach
                          </select>
                          @break
                      @case('unique')
                          <select  class="form-control unique" data-table="{{$field['filter']['table']}}" data-column="{{$field['filter']['column']}}" name="{{$name}}[]" id="ft_{{$dtname}}_{{$name}}" multiple="multiple">
                              
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
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                        </div>
                        <input style="width:100px;" autocomplete="off" type="text" class=" form-control date" name="{{$name}}[start]" id="ft_{{$dtname}}_{{$name}}_start" value="{{fdate(request('ft_'.$dtname.'_'.$name.'_start'))}}">
                        <div class="input-group-prepend">
                          <span class="input-group-text">-</span>
                        </div>
                        <input style="width:100px;" autocomplete="off" type="text" class=" form-control date" name="{{$name}}[end]" id="ft_{{$dtname}}_{{$name}}_end" value="{{fdate(request('ft_'.$dtname.'_'.$name.'_end'))}}">
                      </div>
                          @break
                      @case('currency')
                      <div class="input-group">
                        <input style="width:100px;" autocomplete="off" type="text" class=" form-control" name="{{$name}}[start]" id="ft_{{$dtname}}_{{$name}}_start" value="{{fdate(request('ft_'.$dtname.'_'.$name.'_start'))}}"
                        data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                        <div class="input-group-prepend">
                          <span class="input-group-text">-</span>
                        </div>
                        <input style="width:100px;" autocomplete="off" type="text" class=" form-control" name="{{$name}}[end]" id="ft_{{$dtname}}_{{$name}}_end" value="{{fdate(request('ft_'.$dtname.'_'.$name.'_end'))}}"
                        data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                      </div>
                          @break
                      @default
                          <input autocomplete="off" type="text" class=" form-control " name="{{$name}}" id="ft_{{$dtname}}_{{$name}}" placeholder="{{$label}}" value="{{request('ft_'.$dtname.'_'.$name)}}">
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
              <button type="submit" id="btn-filter-{{$dtname}}" class="btn btn-info btn-filter"> {{__('Filter')}}</button>
              <button type="button" id="btn-clear-{{$dtname}}" class="btn btn-default" > {{__('Clear')}}</button>
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
<script src="{{asset('plugins/inputmask/min/jquery.inputmask.bundle.min.js')}}"></script>
<script type="text/javascript">
$(function () {
    $('.select2').select2();
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    });
    $('[data-mask]').inputmask();
    $('.date').daterangepicker({
      singleDatePicker:true,
      showDropdowns:true,
      drops:'auto',
      locale: {
        format: 'DD-MM-YYYY'
      }
    })
    $('.date').val('');
    $('#btn-clear-{{$dtname}}').click(function(e){
      $('.date').val('');
      $('input').val('');
      $("select").val("");
      $("select").trigger("change");
      $('#btn-filter-{{$dtname}}').trigger("click");
    });
    $('.unique').each(function(){
      var table = $(this).attr('data-table');
      var column = $(this).attr('data-column');
      $(this).select2({
      theme: 'bootstrap4',
      placeholder: '',
      minimumInputLength: 2,
      ajax: {
      url: BASE_URL+'/json/search',
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term,
          table: table,
          column: column,
        };
      },
      processResults: function (data) {
        return {
          results:  $.map(data, function (item) {
            return {
              text: item,
              id: item
            }
          })
        };
      },
      cache: true
    }
    })
    })
    
    
  })
</script>
@endpush