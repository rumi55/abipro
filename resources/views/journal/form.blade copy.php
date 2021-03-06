@php 
$type = $journal->is_voucher==1?'vouchers':'journals';
$title_type = $journal->is_voucher==1?'Voucher':'Jurnal';
$breadcrumbs = array(
    ['label'=>$title_type, 'url'=>route('dcru.index',$type)],
    ['label'=>($mode=='edit'?'Edit ':'Buat ').$title_type],
);
@endphp
@extends('layouts.app')
@section('title', ($mode=='edit'?'Edit ':'Buat ').$title_type)

@section('content')
<form method="POST" action="{{$mode=='edit'?route('journals.edit.update', $journal->id):route('journals.create.save')}}">
@csrf
@if($mode=='edit')
@method('PUT')
@endif
<div class="card">
    <div class="card-header">
        <h5 class="card-title">{{$title_type}} {{$mode=='create'?'Baru':'#'.$journal->trans_no}}</h5>
    </div>
    <div class="card-body pb-1">    
        <div class="row">    
            <div class="col-md-3">
                <div class="form-group">
                    <label for="trans_no" >Group Transaksi</label>
                    <input name="auto" value="1" type="hidden">
                    <select name="numbering_id" class="form-control select2 @error('numbering_id') is-invalid @enderror" style="width:100%">
                        @foreach($numberings as $numbering)
                        <option {{$numbering->id==old('numbering_id', $journal->numbering_id)?'selected':''}} value="{{$numbering->id}}">{{$numbering->name}}</option>
                        @endforeach
                    </select>
                    @error('numbering_id')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>    
            <div class="col-md-3">
                <div class="form-group">
                    <label for="trans_no" >Nomor</label>
                    <input id="trans_no" readonly name="trans_no" type="text" class="form-control" value="@if($mode=='edit') {{old('trans_no',$journal->trans_no)}} @else [Automatic] @endif" >
                </div>
            </div>    
            <div class="col-md-3">
                <div class="form-group">
                    <label for="trans_date">Tanggal</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>
                        <input id="trans_date" name="trans_date" type="text" class="form-control date @error('trans_date') is-invalid @enderror" value="{{fdate(old('trans_date',$journal->trans_date))}}" >
                    </div>
                    @error('trans_date')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>   
            <div class="col-md-3">
                <div class="form-group">
                    <label>Penerima</label>
                    <select name="contact_id" class="form-control select2">
                        <option value="">--Pilih Kontak--</option>
                        @foreach($contacts as $contact)
                        <option {{old('contact_id', $journal->contact_id)==$contact->id?'selected':''}} value="{{$contact->id}}">{{$contact->name}}</option>
                        @endforeach
                    </select>
                    @error('contact_id')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div> 
        </div>

        <div class="row">    
            <div class="col-md-6">
                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"  rows="1" style="width:100%">{{old('description',$journal->description)}}</textarea >
                    @error('description')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>    
            <div class="col-md-3">
            </div>    
            <div class="col-md-3">
            </div>    
        </div>
        <div class="table-responsive mt-4">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Akun</th>
                        <th>Keterangan</th>
                        <th class="text-right">Debet</th>
                        <th class="text-right">Kredit</th>
                        <th>Departemen</th>
                        <th>Sortir</th>
                        <th class="text-right"></th>
                    </tr>
                </thead>
                <tbody id="tbody">
                @php 
                $details = $journal->details; 
                $row_count = old('detail_length', count($details));
                $row_count = $row_count==0?2:$row_count;
                @endphp
                @for($i=0;$i<$row_count;$i++)
                @php 
                if(count($details)==0){
                    $detail = new \App\JournalDetail;
                }else{
                    $detail = $details[$i] ;
                }
                @endphp
                    <tr id="row_{{$i}}" data-index="{{$i}}">
                        <td>
                            <select id="detail_account_id_{{$i}}" name="detail_account_id_{{$i}}" class="form-control select2" style="width:300px">
                                @foreach($accounts as $account)
                                <option {{old('detail_account_id_'.$i, $detail->account_id)==$account->id?'selected':''}} value="{{$account->id}}">{{$account->account_name}}</option>
                                @endforeach
                            </select>
                            @error('detail_account_id_'.$i)<small class="text-danger">{!!$message!!}</small>@enderror
                            @if($mode=='edit')
                            <input type="hidden" name="detail_id_{{$i}}" value="{{$detail->id}}"/>
                            @endif
                        </td>
                        <td>
                            <textarea rows="1" style="width:200px" id="detail_description_{{$i}}" name="detail_description_{{$i}}" data-index="{{$i}}" class="form-control">{{$detail->description}}</textarea>
                            @error('detail_description_'.$i)<small class="text-danger">{!!$message!!}</small>@enderror
                        </td>
                        <td class="text-right">
                            <input name="detail_debit_{{$i}}" data-index="{{$i}}" required type="text" id="detail_debit_{{$i}}" class="form-control debit @error('detail_debit_'.$i) is-invalid @enderror" value="{{old('detail_debit_'.$i, $detail!=null?$detail->debit:'')}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('detail_debit_'.$i)<small class="text-danger">{!!$message!!}</small>@enderror
                        </td>
                        <td class="text-right">
                            <input name="detail_credit_{{$i}}" data-index="{{$i}}" required type="text" id="detail_credit_{{$i}}" class="form-control credit @error('detail_credit_'.$i) is-invalid @enderror" value="{{old('detail_credit_'.$i, $detail!=null?$detail->credit:'')}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                            @error('detail_credit_'.$i)<small class="text-danger">{!!$message!!}</small>@enderror
                        </td>
                        <td>
                        <select id="detail_department_id_{{$i}}" name="detail_department_id_{{$i}}" class="form-control select2" style="width:200px">
                            <option {{empty(old('detail_department_id_'.$i, $detail->department_id))?'selected':''}} value="">--Pilih Departemen--</option>
                            @foreach($departments as $department)
                            <option {{old('detail_department_id_'.$i, $detail->department_id)==$department->id?'selected':''}} value="{{$department->id}}">{{$department->name}}</option>
                            @endforeach
                        </select>
                        @error('detail_department_id_'.$i)<small class="text-danger">{!!$message!!}</small>@enderror
                        </td>
                        <td>
                        @php 
                        $strtags = old('detail_tags_'.$i, $detail->tags);
                        $arrtags = explode(',', $strtags); 
                        @endphp
                        <input type="hidden" id="detail_tags_{{$i}}" name="detail_tags_{{$i}}" value="{{$strtags}}" />
                        <select id="detail_select_tags_{{$i}}" data-index="{{$i}}" multiple class="form-control select2 sortir-select" style="width:200px">
                            @php 
                            $optgroup = ''; 
                            $citem = count($tags);
                            @endphp
                            @foreach($tags as $j => $tag)
                                @if($optgroup!=$tag->group)
                                @if($optgroup!='')
                                </optgroup>
                                @endif
                                <optgroup label="{{$tag->group}}">
                                @php $optgroup = $tag->group; @endphp
                                @endif
                                <option {{in_array($tag->id,$arrtags)?'selected':''}} value="{{$tag->id}}">{{$tag->item_id.' - '.$tag->item_name}}</option>
                                @if($j==$citem)
                                </optgroup>
                                @endif
                            @endforeach
                        </select>
                        @error('detail_tag_'.$i)<small class="text-danger">{!!$message!!}</small>@enderror
                        </td>
                        <td><button type="button" class="btn btn-link btn-sm text-danger btn-remove" data-index="{{$i}}"><i class="fas fa-trash-alt"></i></button></td>
                    </tr>
                @endfor
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">
                            <button id="add-btn" type="button" class="btn btn-secondary btn-sm"><i class="fas fa-plus"></i> Tambah Transaksi</button>
                        </th>
                        <th class="text-right">
                            <small class="text-muted">Total Debet</small><br>
                            <input name="total_debit" type="text" class="total form-control-plaintext text-success text-bold" readonly id="total_debit" value="{{old('total_debit', $journal->total)}}" data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask/>
                            @error('total_debit')<small class="text-danger">{!! $message !!}</small>@enderror
                        </th>
                        <th class="text-right">
                            <small class="text-muted">Total Kredit</small><br>
                            <input name="total_credit" type="text" class="total form-control-plaintext text-success text-bold" readonly id="total_credit" value="{{old('total_credit', $journal->total)}}" data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask/>
                            @error('total_credit')<small class="text-danger">{!! $message !!}</small>@enderror
                            <input type="hidden" id="total" name="total" value="{{old('total', $journal->total)}}" />
                            <input type="hidden" name="is_voucher" value="{{old('is_voucher', $journal->is_voucher)}}" />
                            <input id="detail_length" type="hidden" name="detail_length" value="{{$row_count}}" />
                        </th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-sm-6">
                <a href="{{route('dcru.index', 'journals')}}" class="btn btn-default" >Batal</a>
            </div>
            <div class="col-sm-6 text-right">
                <button id="btn-save" type="submit" class="btn btn-primary" >{{$mode=='edit'?'Simpan':'Buat '.$title_type}}</button>
            </div>
        </div>
    </div>
</div>
</form>
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
function  init(){
    $('.date').daterangepicker({
      timePicker: false,
      singleDatePicker:true,
      autoApply:true,
      locale: {
        format: 'DD-MM-YYYY'
      }
    });
    $('.currency').inputmask({ 'alias': 'currency' })
    $('[data-mask]').inputmask();
    $(".select2").select2({theme: 'bootstrap4'});
    
    $('.sortir-select').change(function(e){
        var idx = $(this).attr('data-index');
        $('#detail_tags_'+idx).val($(this).val().join())
    })
    $('.btn-remove').click(function(e){
        e.preventDefault();
        var idx = $(this).attr('data-index');
        if(idx>1){
            $('#row_'+idx).remove();
            var len = $('#detail_length').val();
            $('#detail_length').val(len-1);
        }else{
            $('#detail_debit_'+idx).val(0);
            $('#detail_credit_'+idx).val(0);
            $('#detail_description_'+idx).val('');
        }
        onchange();
    })
}
$(function () {
    init()
    $('#form').submit(function(e){
      if(validate()){
        e.preventDefault();return;
      }
    })
    $('#add-btn').click(function(e){
        var idx = $('#detail_length').val();
        var last_idx = parseInt(idx)-1;
        var row = `
                    <tr id="row_${idx}">
                        <td>
                            <select name="detail_account_id_${idx}" class="form-control select2" style="width:300px">
                                @foreach($accounts as $account)
                                <option value="{{$account->id}}">{{$account->account_name}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <textarea id="detail_description_${idx}" name="detail_description_${idx}" data-index="${idx}" class="form-control" rows="1" cols="100" style="width:200px"></textarea>
                        </td>
                        <td class="text-right">
                            <input name="detail_debit_${idx}" data-index="${idx}" required type="text" id="detail_debit_${idx}" class="form-control debit " value="0"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                        </td>
                        <td class="text-right">
                            <input name="detail_credit_${idx}" data-index="${idx}" required type="text" id="detail_credit_${idx}" class="form-control credit" value="0"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                        </td>
                        <td>
                        <select name="detail_department_id_${idx}" class="form-control select2" style="width:200px">
                        <option value="">--Pilih Departemen--</option>
                            @foreach($departments as $department)
                                <option value="{{$department->id}}">{{$department->name}}</option>
                            @endforeach
                        </select>
                        </td>
                        <td>
                        <input type="hidden" id="detail_tags_${idx}" name="detail_tags_${idx}" />
                        <select  multiple class="form-control select2" style="width:200px">
                            @php 
                            $optgroup = ''; 
                            $citem = count($tags);
                            @endphp
                            @foreach($tags as $j => $tag)
                                @if($optgroup!=$tag->group)
                                @if($optgroup!='')
                                </optgroup>
                                @endif
                                <optgroup label="{{$tag->group}}">
                                @php $optgroup = $tag->group; @endphp
                                @endif
                                <option value="{{$tag->id}}">{{$tag->item_id.' - '.$tag->item_name}}</option>
                                @if($j==$citem)
                                </optgroup>
                                @endif
                            @endforeach
                        </select>
                        </td>
                        <td><button type="button" class="btn btn-link btn-sm text-danger btn-remove" data-index="${idx}"><i class="fas fa-trash-alt"></i></button></td>
                    </tr>
        `;        
        $('#tbody').append(row);
        $('#detail_length').val(last_idx+2);
        onchange()
        init();
    });
    $('.debit').change(function(){
        var idx = $(this).attr('data-index');
        var val = parseNumber($(this).val());
        if(val>0){
            $('#detail_credit_'+idx).val(0);
        }
        onchange()
    })
    $('.credit').change(function(){
        var idx = $(this).attr('data-index');
        var val = parseNumber($(this).val());
        if(val>0){
            $('#detail_debit_'+idx).val(0);
        }
        onchange()
    })
});
function validate(){
  return invalid;
}
function onchange(){
    var debit = sum('.debit');
    var credit = sum('.credit');
    $('#total_debit').val(debit);
    $('#total_credit').val(credit);
    if(debit==credit){
        $('.total').addClass('text-success');
        $('.total').removeClass('text-danger');
        $('#total').val(debit);
        $('#btn-save').prop('disabled', false);
    }else{
        $('.total').addClass('text-danger');
        $('.total').removeClass('text-success');
        $('#btn-save').prop('disabled', true);
    }
}
function sum(selector){
    var total = 0;
   $(selector).each(function(index){
      var val = $(this).val();
      if(val==''||val==null){
        val = 0;
        $(this).val(val)
      }
      var n = parseNumber(val);
      total=total+n;
    });
    return total;
}
function parseNumber(val){
    if(val=='' || val==null)return 0;
    return parseInt(val.split('.').join(''));
}
</script>
@endpush