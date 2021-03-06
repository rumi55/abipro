@component('components.card_form',['id'=>'filter-form', 'btn_label'=>'Filter', 'method'=>'GET', 'action'=>route('reports.sortirs')])
<div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <label>Jenis Laporan</label>
        <select id="layout" class="select2" name="layout">
          <option {{request('layout', 'detail')=='detail'?'selected':''}} value="detail">Detail</option>
          <option {{request('layout')=='summ'?'selected':''}} value="summ">Ringkas</option>
        </select>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label>Sortir</label>
        <select style="width:100%" id="select-sortir" data-selected="{{request('sortir')}}" name="sortir" class="select2"></select>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
      @php $val = request('sortir_items',[]); $val = implode(',', $val); @endphp
        <label>Item Sortir</label>
        <select style="width:100%" id="select-item-sortir" data-selected="{{$val}}" name="sortir_items[]" class="select2" multiple></select>
      </div>
    </div>
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
        <select id="select-account" data-selected="{{$val}}" name="accounts[]" class="select2" multiple></select>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
      @php $val = request('departments',[]); $val = implode(',', $val); @endphp
        <label>Departemen</label>
        <select id="select-department" data-selected="{{$val}}" name="departments[]" class="select2" multiple></select>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label>Tampilkan Kolom</label>
        <div class="mt-1">
          <div class="icheck-success d-inline">
            <input type="checkbox" name="department" value="1" id="department"  {{request('department')=='1'?'checked':''}} >
            <label for="department">
              Departemen
            </label>
          </div>
          <div class="icheck-success d-inline">
            <input type="checkbox" name="created_by" value="1" id="created_by" {{request('created_by')=='1'?'checked':''}} >
            <label for="created_by">
              Dibuat Oleh
            </label>
          </div>
        </div>
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
function select2Load(selector, url, data={}){$.ajax({url: url,data:data,dataType: 'json',success: function(res){$(selector).select2({theme: 'bootstrap4',data:res});var val = $(selector).attr('data-selected');if($(selector).prop('multiple') && val!=""){$(selector).val(val.split(','));}else{$(selector).val(val);}$(selector).trigger('change');}})}
var sortir = [];
function loadSortir(){
  $.ajax({
    url: "{{route('select2', 'sortirs')}}",
    dataType: 'json',
    success: function(res){
      sortir = res;
      var group = [];
      var first_id = null;
      for(var i=0;i<res.length;i++){
        var dt = res[i];
        group.push({id: dt.text, text:dt.text});
        first_id = i==0?dt.text:first_id;
      }
      var selector = '#select-sortir';
      $(selector).select2({theme: 'bootstrap4',data:group});
      var val = $(selector).attr('data-selected');
      if(val=='' && group.length>0){
        val = first_id;
      }
      $(selector).val(val);
      $(selector).trigger('change');
      $(selector).change(function(e){
        var v = $(this).val();
        $('#select-item-sortir').val(null).empty().select2('destroy');
        $('#select-item-sortir').select2({theme: 'bootstrap4',data:sortir.filter(function(item){return item.text==v})});
      })
      $('#select-item-sortir').select2({theme: 'bootstrap4',data:sortir.filter(function(item){return item.text==val})});
    }
  })
}
$(function () {
  select2Load('#select-account', "{{route('select2', ['name'=>'accounts'])}}", {has_children:0})
  select2Load('#select-department', "{{route('select2', ['name'=>'departments'])}}")
  loadSortir();
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