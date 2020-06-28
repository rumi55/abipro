@php 
$active_menu='settings'; 
$breadcrumbs = array(
    ['label'=>'Settings']
);
@endphp
@extends('layouts.app')
@section('title', trans('Settings'))
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-3">
      @include('setting._menu', ['active'=>'account_mappings'])
    </div>
    <div class="col-md-9">
      <div class="card">
        <form action="{{route('settings.account_mapping.save')}}" method="POST" >
        @csrf
        <div class="card-body">
          @foreach($mappings as $mapping)
            <div class="form-group">
              <label>{{tt($mapping, 'name')}}:</label>
              @php 
              if(array_key_exists($mapping->id,$account_mappings)){
                $value= $account_mappings[$mapping->id];
              }else{
                $value='';
              } 
              @endphp
              <select id="mapping_{{$mapping->id}}" data-mapping="{{$mapping->id}}" name="mapping_{{$mapping->id}}" class="form-control select2" data-value="{{$value??''}}"></select>
            </div>
          @endforeach
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush
@push('js')
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/inputmask/min/jquery.inputmask.bundle.min.js')}}"></script>
<script type="text/javascript">


$(function () {
  $.ajax({
        url: "{{route('json.output', 'accounts')}}",
        dataType: 'json',
        success: function(res){
            var accounts = $.map(res.data, function (item) {
                return {
                    id: item.id,
                    text: '('+item.account_no+') '+item.account_name,
                    main_desc: item.account_name,
                    left_desc: item.account_no,
                    right_desc: item.account_type.name
                }
            })
            $('.select2').each(function(index){
              setSelect2(this,accounts)
            })
        }
    });
});
function formatResults(item) {
  if(item.main_desc==undefined){
    return item;
  }
  var $item = $(
    `
        <div class="row">
            <div class="col">${item.main_desc}</div>
        </div>
        <div class="row font-weight-light">
            <div class="col">${item.left_desc}</div>
            <div class="col text-right">${item.right_desc}</div>
        </div>
    `
  );
  return $item;
};
function setSelect2(selector, data, disabled=false, placeholder='Select', clear=true){
    $(selector).val(null).empty();//.select2('destroy');
    $(selector).select2({
        theme: 'bootstrap4',
        data:data, 
        placeholder: placeholder,
        allowClear: clear,
        templateResult: formatResults,
        disabled: disabled
    });
    var val = $(selector).attr('data-value');
    $(selector).val(val);
    $(selector).trigger('change');
}
</script>

@endpush