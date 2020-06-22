@component('components.card_form',['id'=>'filter-form', 'btn_label'=>'Filter', 'method'=>'GET', 'action'=>route('reports.cashflow')])
<div class="row">
<div class="col-md-4">
      <div class="form-group">
        <label>Tanggal</label>
        <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">
                  <i class="far fa-calendar-alt"></i>
              </span>
            </div>
            <input type="text" value="{{request('start_date')}}" class="form-control datepicker" name="start_date" id="start_date">
            <div class="input-group-prepend">
            <span class="input-group-text">
                s.d
            </span>
            </div>
            <input type="text" value="{{request('end_date')}}" class="form-control datepicker" name="end_date" id="end_date">
        </div>
        <small id="start_date_error" class="text-danger"></small>
        <small id="end_date_error" class="text-danger"></small>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
      @php $val = request('accounts',[]); $val = implode(',', $val); @endphp
        <label>Akun</label>
        <select id="select-journal" data-selected="{{$val}}" name="accounts[]" class="select2" multiple></select>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
      @php $val = request('departments',[]); $val = implode(',', $val); @endphp
        <label>Departemen</label>
        <select id="select-department" data-selected="{{$val}}" name="departments[]" class="select2" multiple></select>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
      @php $val = request('sortirs',[]); $val = implode(',', $val); @endphp
        <label>Sortir</label>
        <select id="select-sortir" data-selected="{{$val}}" name="sortirs[]" class="select2" multiple></select>
      </div>
    </div>  
</div>
@endcomponent
@push('css')
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush
@push('js')
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
function select2Load(selector, url){
  $.ajax({
    url: url,
      dataType: 'json',
      success: function(res){
        $(selector).select2({theme: 'bootstrap4',data:res});
        var val = $(selector).attr('data-selected');
        if($(selector).prop('multiple') && val!=""){
          $(selector).val(val.split(','));
        }else{
          $(selector).val(val);
        }
        $(selector).trigger('change');
      }
  })
}
$(function () {
  select2Load('#select-journal', "{{route('select2', ['name'=>'accounts'])}}")
  select2Load('#select-department', "{{route('select2', ['name'=>'departments'])}}")
  select2Load('#select-sortir', "{{route('select2', ['name'=>'sortirs'])}}")
  
  $('.select2').select2({theme: 'bootstrap4'});
    $('.datepicker').daterangepicker({
      timePicker: false,
      singleDatePicker:true,
      autoApply:true,
      locale: {
        format: 'DD-MM-YYYY'
      }
    });
    // $(".select2").select2({theme: 'bootstrap4'});
    $('#start_date').change(function(){
      validate();
    });
    $('#end_date').change(function(){
      validate();  
    });
    $('#filter-form').submit(function(e){
      if(validate()){
        e.preventDefault();return;
      }
    })
});
function validate(){
  var start_val = $('#start_date').val();
  var end_val = $('#end_date').val();
  var sdate = moment(start_val, "DD-MM-YYYY");
  var edate = moment(end_val, "DD-MM-YYYY");
  invalid = edate.isBefore(sdate);
  if(invalid){
    $('#end_date_error').html('Tanggal akhir tidak boleh lebih kecil dari tanggal awal');    
    $('#filter-form-btn').addClass('disabled');
    $('#filter-form-btn').attr('aria-disabled', 'true');
  }else{
    $('#end_date_error').html('');    
    $('#filter-form-btn').removeClass('disabled');
    $('#filter-form-btn').attr('aria-disabled', 'false');
  }
  return invalid;
}
</script>
@endpush