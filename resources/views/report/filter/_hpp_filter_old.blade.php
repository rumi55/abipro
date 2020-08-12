@component('components.card_form',['id'=>'filter-form', 'btn_label'=>'Filter', 'method'=>'GET', 'action'=>route('reports.hpp')])
<div class="row">
    <div class="col-md-3">
      <div class="form-group">
        <label>Jenis Perbandingan</label>
        <select id="compare" class="select2" name="compare">
          <option {{request('compare', 'period')=='period'?'selected':''}} value="period">Periode</option>
          <option {{request('compare')=='department'?'selected':''}} value="department">Departemen</option>
          <option {{request('compare')=='budget'?'selected':''}} value="budget">Anggaran</option>
        </select>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label>Periode</label>
        <select id="period" class="select2" name="period">
          <option {{request('period')=='daily'?'selected':''}}      value="daily">Harian</option>
          <option {{request('period', 'monthly')=='monthly'?'selected':''}}    value="monthly">Bulanan</option>
          <option {{request('period')=='quarterly'?'selected':''}}  value="quarterly">Triwulanan</option>
          <option {{request('period')=='semiyearly'?'selected':''}} value="semiyearly">Semitahunan</option>
          <option {{request('period')=='yearly'?'selected':''}}     value="yearly">Tahunan</option>
        </select>
      </div>
    </div>
    @php 
    $start_date = request('start_date', date('d-m-Y'));
    $date = explode('-', $start_date);
    $y = date('Y');
    $m = date('m');
    if(count($date)==3){
      $y = $date[2];
      $m=$date[1];
    }
    @endphp
    <div class="col-md-3">
      <div class="form-group">
        <label id="label-period">Bulan</label>
        <div id="group-month" class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="far fa-calendar-alt"></i>
            </span>
          </div>
          <select id="select-m" class="select2">
            <option {{$m == '01' ? 'selected':'' }} value="01">Januari</option>
            <option {{$m == '02' ? 'selected':'' }} value="02">Februari</option>
            <option {{$m == '03' ? 'selected':'' }} value="03">Maret</option>
            <option {{$m == '04' ? 'selected':'' }} value="04">April</option>
            <option {{$m == '05' ? 'selected':'' }} value="05">Mei</option>
            <option {{$m == '06' ? 'selected':'' }} value="06">Juni</option>
            <option {{$m == '07' ? 'selected':'' }} value="07">Juli</option>
            <option {{$m == '08' ? 'selected':'' }} value="08">Agustus</option>
            <option {{$m == '09' ? 'selected':'' }} value="09">September</option>
            <option {{$m == '10' ? 'selected':'' }} value="10">Oktober</option>
            <option {{$m == '11' ? 'selected':'' }} value="11">November</option>
            <option {{$m == '12' ? 'selected':'' }} value="12">Desember</option>
          </select>
          <select class="select2 select-y">
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
        <div id="group-date" class="input-group" style="display:none">
            <div class="input-group-prepend">
              <span class="input-group-text">
                  <i class="far fa-calendar-alt"></i>
              </span>
            </div>
            <input type="text" value="{{$start_date}}" class="form-control float-right datepicker" name="start_date" id="start_date">
        </div>
        <small id="start_date_error" class="text-danger"></small>
      </div>
    </div>  
    <div class="col-md-3 period-container">
      <div class="form-group">
        <label>Perbandingan Periode</label>
        <select class="select2" name="compare_period">
          <option value="">&nbsp;</option>
          <option {{request('compare_period')=='1'?'selected':''}}      value="1">1 periode sebelumnya</option>
          <option {{request('compare_period')=='2'?'selected':''}}      value="2">2 periode sebelumnya</option>
          <option {{request('compare_period')=='3'?'selected':''}}      value="3">3 periode sebelumnya</option>
          <option {{request('compare_period')=='4'?'selected':''}}      value="4">4 periode sebelumnya</option>
          <option {{request('compare_period')=='5'?'selected':''}}      value="5">5 periode sebelumnya</option>
          <option {{request('compare_period')=='6'?'selected':''}}      value="6">6 periode sebelumnya</option>
          <option {{request('compare_period')=='7'?'selected':''}}      value="7">7 periode sebelumnya</option>
          <option {{request('compare_period')=='8'?'selected':''}}      value="8">8 periode sebelumnya</option>
          <option {{request('compare_period')=='9'?'selected':''}}      value="9">9 periode sebelumnya</option>
          <option {{request('compare_period')=='10'?'selected':''}}      value="10">10 periode sebelumnya</option>
          <option {{request('compare_period')=='11'?'selected':''}}      value="11">11 periode sebelumnya</option>
        </select>
      </div>
      <div class="mt-1">
          <div class="icheck-success d-inline">
            <input type="checkbox" name="cumulative" value="1" id="cumulative"  {{request('cumulative')=='1'?'checked':''}}>
            <label for="cumulative">
              Jumlah Kumulatif
            </label>
          </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
      @php $val = request('departments',[]); $val = implode(',', $val); @endphp
        <label>Departemen</label>
        <select id="select-department" style="width:100%" data-selected="{{$val}}" name="departments[]" class="select2" multiple></select>
      </div>
    </div>
    <div class="col-md-3">
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
$(function () {
  $('.select2').select2({theme: 'bootstrap4'});
  $('.datepicker').daterangepicker({timePicker: false,singleDatePicker:true,autoApply:true,showDropdowns:true,linkedCalendars:true,minYear: 2017,maxYear: {{date('Y')+2}},locale: {format: 'DD-MM-YYYY'}});
  select2Load('#select-department', "{{route('select2', ['name'=>'departments'])}}");
  $('#compare').change(onchangeCompare);
  onchangeCompare();
  onchangeDate();
  $('#period').change(onchangeDate);
  $('#select-m').change(onchangeDate);
  $('.select-y').change(onchangeDate);
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