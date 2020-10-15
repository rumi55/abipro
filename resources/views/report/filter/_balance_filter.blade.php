<div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label>Jenis Perbandingan</label>
        <select id="compare" class="select2" name="compare">
          <option {{request('compare', 'period')=='period'?'selected':''}} value="period">Periode</option>
          <option {{request('compare')=='department'?'selected':''}} value="department">Departemen</option>
          <option {{request('compare')=='ratio'?'selected':''}} value="ratio">Analisis Rasio</option>
        </select>
      </div>
    </div>

    @php
    $start_month = request('start_month', '01');
    $end_month = request('end_month', date('m'));
    $y = request('year', date('d-m-Y'));
    @endphp
    <div class="col-md-12">
      <div class="form-group">
        <label id="label-period">Bulan</label>
        <div id="group-month" class="input-group">

          <select id="select-m" class="select2" name="start_month">
            <option {{$start_month == '01' ? 'selected':'' }} value="01">Januari</option>
            <option {{$start_month == '02' ? 'selected':'' }} value="02">Februari</option>
            <option {{$start_month == '03' ? 'selected':'' }} value="03">Maret</option>
            <option {{$start_month == '04' ? 'selected':'' }} value="04">April</option>
            <option {{$start_month == '05' ? 'selected':'' }} value="05">Mei</option>
            <option {{$start_month == '06' ? 'selected':'' }} value="06">Juni</option>
            <option {{$start_month == '07' ? 'selected':'' }} value="07">Juli</option>
            <option {{$start_month == '08' ? 'selected':'' }} value="08">Agustus</option>
            <option {{$start_month == '09' ? 'selected':'' }} value="09">September</option>
            <option {{$start_month == '10' ? 'selected':'' }} value="10">Oktober</option>
            <option {{$start_month == '11' ? 'selected':'' }} value="11">November</option>
            <option {{$start_month == '12' ? 'selected':'' }} value="12">Desember</option>
          </select>
          <div class="input-group-prepend">
            <span class="input-group-text">
                -
            </span>
          </div>
          <select id="select-m2" class="select2" name="end_month">
            <option {{$end_month == '01' ? 'selected':'' }} value="01">Januari</option>
            <option {{$end_month == '02' ? 'selected':'' }} value="02">Februari</option>
            <option {{$end_month == '03' ? 'selected':'' }} value="03">Maret</option>
            <option {{$end_month == '04' ? 'selected':'' }} value="04">April</option>
            <option {{$end_month == '05' ? 'selected':'' }} value="05">Mei</option>
            <option {{$end_month == '06' ? 'selected':'' }} value="06">Juni</option>
            <option {{$end_month == '07' ? 'selected':'' }} value="07">Juli</option>
            <option {{$end_month == '08' ? 'selected':'' }} value="08">Agustus</option>
            <option {{$end_month == '09' ? 'selected':'' }} value="09">September</option>
            <option {{$end_month == '10' ? 'selected':'' }} value="10">Oktober</option>
            <option {{$end_month == '11' ? 'selected':'' }} value="11">November</option>
            <option {{$end_month == '12' ? 'selected':'' }} value="12">Desember</option>
          </select>
          <select class="select2 select-y" name="year">
          @for($year=intVal(date('Y'));$year>=2010;$year--)
            <option {{$y==$year?'selected':''}} value="{{$year}}">{{$year}}</option>
          @endfor
          </select>
        </div>
        <div id="group-year" class="input-group" style="display:none">
          <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="far fa-calendar-alt"></i>
            </span>
          </div>
          <select class="select2 select-y">
          @for($year=intVal(date('Y'));$year>=2010;$year--)
            <option value="{{$year}}">{{$year}}</option>
          @endfor
          </select>
        </div>
        <div class="mt-1">
            <div class="icheck-success d-inline">
              <input type="checkbox" name="total_year" value="1" id="total_year"  {{request('total_year')=='1'?'checked':''}}>
              <label for="total_year">
                Total Tahun Berjalan
              </label>
            </div>
            <div class="icheck-success d-inline">
              <input type="checkbox" name="total_last_year" value="1" id="total_last_year"  {{request('total_last_year')=='1'?'checked':''}}>
              <label for="total_last_year">
                Total Tahun Sebelumnya
              </label>
            </div>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="form-group">
      @php $val = request('departments',[]); $val = implode(',', $val); @endphp
        <label>Departemen</label>
        <select id="select-department" style="width:100%" data-selected="{{$val}}" name="departments[]" class="select2" multiple></select>
      </div>
    </div>
    <div class="col-md-12">
      <div class="form-group">
        <label>Subakun</label>
        <select class="select2" name="subaccount">
          <option {{request('subaccount')==0?'selected':''}} value="0">Tanpa Subakun</option>
          <option {{request('subaccount')==1?'selected':''}} value="1">Level 1</option>
          <option {{request('subaccount')==2?'selected':''}} value="2">Level 2</option>
        </select>
      </div>
    </div>
</div>
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
$(function () {
  $('.select2').select2({theme: 'bootstrap4'});
  $('.datepicker').daterangepicker({timePicker: false,singleDatePicker:true,autoApply:true,showDropdowns:true,linkedCalendars:true,minYear: 2017,maxYear: {{date('Y')+2}},locale: {format: 'DD-MM-YYYY'}});
  select2Load('#select-department', "{{route('select2', ['name'=>'departments'])}}");
  $('#compare').change(onchangeCompare);
//   onchangeCompare();
//   onchangeDate();
//   $('#period').change(onchangeDate);
//   $('#select-m').change(onchangeDate);
//   $('.select-y').change(onchangeDate);
});
function select2Load(selector, url){$.ajax({url: url,dataType: 'json',success: function(res){$(selector).select2({theme: 'bootstrap4',data:res});var val = $(selector).attr('data-selected');if($(selector).prop('multiple') && val!=""){$(selector).val(val.split(','));}else{$(selector).val(val);}$(selector).trigger('change');}})}
function onchangeCompare(){
  if($('#compare').val()=='period'){
    $('.period-container').show();
  }else if($('#compare').val()=='budget'){
    $('#period').val('monthly');
    $('.period-container').hide();
  }else{
    $('.period-container').hide();
  }
}

function onchangeDate(){
  var period = $('#period').val();
  if(period=='monthly' || period=='quarterly' || period=='semiyearly'){
    $('#group-date').hide();
    $('#group-month').show();
    $('#group-year').hide();
    $('#start_date').val('01-'+$('#select-m').val()+'-'+$('.select-y').val());
    $('#label-period').html('Bulan');
  }else if(period=='yearly'){
    $('#label-period').html('Tahun');
    $('#group-date').hide();
    $('#group-month').hide();
    $('#group-year').show();
    $('#start_date').val('01-01-'+$('.select-y').val());
  }else{
    $('#label-period').html('Tanggal');
    $('#group-date').show();
    $('#group-month').hide();
    $('#group-year').hide();
  }
}
</script>
@endpush
