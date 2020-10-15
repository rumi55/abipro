@component('components.card_form',['id'=>'filter-form', 'btn_label'=>'Filter', 'method'=>'GET', 'action'=>route('reports.ledgers')])
<div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <label>{{__('Transaction Date')}}</label>
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
      <label>{{__('Account')}}</label>
        <select id="select-account" data-selected="{{$val}}" name="accounts[]" class="select2" multiple></select>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
      @php $val = request('departments',[]); $val = implode(',', $val); @endphp
      <label>{{__('Department')}}</label>
        <select id="select-department" data-selected="{{$val}}" name="departments[]" class="select2" multiple></select>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
      @php $val = request('sortirs',[]); $val = implode(',', $val); @endphp
        <label>{{__('Tags')}}</label>
        <select id="select-sortir" data-selected="{{$val}}" name="sortirs[]" class="select2" multiple></select>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
      @php $val = request('created_by',[]); $val = implode(',', $val); @endphp
        <label>{{__('Created by')}}</label>
        <select id="select-created_by" data-selected="{{$val}}" name="created_by[]" class="select2" multiple></select>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label>{{'Order'}}</label>
        <select class="select2" name="sort_key">
            <option {{request('sort_key')=='trans_date'?'selected':''}} value="trans_date">{{__('Transaction Date')}}</option>
            <option {{request('sort_key')=='trans_no'?'selected':''}} value="trans_no">{{__('Transaction No.')}}</option>
              <option {{request('sort_key')=='created_at'?'selected':''}} value="created_at">{{__('Created Date')}}</option>
        </select>
        <div class="mt-1">
          <div class="icheck-success d-inline">
            <input type="radio" name="sort_order" value="asc" id="sort_order_asc"  {{request('sort_order')!='desc'?'checked':''}}>
            <label for="sort_order_asc">
                {{__('Ascending')}}
            </label>
          </div>
          <div class="icheck-success d-inline">
            <input type="radio" name="sort_order" value="desc" id="sort_order_desc"  {{request('sort_order')=='desc'?'checked':''}}>
            <label for="sort_order_desc">
                {{__('Descending')}}
            </label>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label>{{__('Show Columns')}}</label>
        <div class="mt-2">
          <div class="icheck-success d-inline">
            <input type="checkbox" name="department" value="1" id="department"  {{request('department')=='1'?'checked':''}} >
            <label for="department">
                {{__('Department')}}
            </label>
          </div>
          <div class="icheck-success d-inline">
            <input type="checkbox" name="description" value="1" id="description" {{request('description')=='1'?'checked':''}} >
            <label for="description">
                {{__('Description')}}
            </label>
          </div>
          <div class="icheck-success d-inline">
            <input type="checkbox" name="col_tags" value="1" id="col_tags" {{request('col_tags')=='1'?'checked':''}} >
            <label for="col_tags">
                {{__('Tags')}}
            </label>
          </div>
          <div class="icheck-success d-inline">
            <input type="checkbox" name="col_created_by" value="1" id="col_created_by" {{request('col_created_by')=='1'?'checked':''}} >
            <label for="col_created_by">
                {{__('Created by')}}
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
$(function () {
  select2Load('#select-account', "{{route('select2', ['name'=>'accounts'])}}", {has_children:0})
  select2Load('#select-department', "{{route('select2', ['name'=>'departments'])}}")
  select2Load('#select-sortir', "{{route('select2', ['name'=>'sortirs'])}}")
  select2Load('#select-created_by', "{{route('select2', ['name'=>'users'])}}")

  $('.select2').select2({theme: 'bootstrap4'});
    $('.datepicker').daterangepicker({
      timePicker: false,
      singleDatePicker:true,
      autoApply:true,
      showDropdowns:true,linkedCalendars:true,
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
