@php
$type = ($journal->is_voucher==1 && $journal->is_processed==0)?'vouchers':'journals';
$title_type = ($journal->is_voucher==1 && $journal->is_processed==0)?'Voucher':'Journal';
$breadcrumbs = array(
    ['label'=>$title_type, 'url'=>route('dcru.index',$type)],
    ['label'=>($mode=='edit'?__('Edit'):__('Create')).' '.$title_type],
);
@endphp
@extends('layouts.app')
@section('title', ($mode=='edit'?__('Edit'):__('Create')).' '.$title_type)

@section('content')
<form method="POST" action="{{$mode=='edit'?route('journals.edit.update', $journal->id):route('journals.create.save')}}">
@csrf
@if($mode=='edit')
@method('PUT')
@endif
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{$mode=='create'?__('New '.$title_type): __($title_type).'#'.$journal->trans_no}}</h3>
    </div>
    <div class="card-body pb-1">
        @if($journal->status=='rejected')
        <div class="callout callout-danger">
        <p>{{$journal->rejection_note}}</p>
          </div>
          @endif

        <div class="row">
            @if($mode=='create')
            <div class="col-md-4">
                <div class="form-group">
                    <label for="trans_no" >{{__('Transaction Group')}}</label>
                    <div class="input-group">
                        <select id="numbering_id" name="numbering_id" data-numbering-type="journal" data-value="{{old('numbering_id', $journal->numbering_id)}}" class="form-control numbering_id @error('numbering_id') is-invalid @enderror">

                        </select>
                    </div>
                    <small class="text-muted" style="{{($mode=='edit' ||  (old('manual')!=null && old('manual')==1))?'display:none':''}}">{{__('Select numbering format. Transaction number will be generated automatically based on selected numbering format.')}}</small>
                    <br>@error('numbering_id')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
            @endif
            <div class="col-md-4">
                <div class="form-group">
                    <label for="trans_no" >{{__('Transaction No.')}}</label>
                    @if($mode=='create')
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                            <input id="manual" type="checkbox" {{($mode=='edit' ||  (old('manual')!=null && old('manual')==1))?'checked':''}} value="1" name="manual" aria-label="manual"> <label for="manual">Manual</label>
                          </div>
                        </div>
                        <input  id="trans_no_auto" readonly style="{{($mode=='edit' || (old('manual')!=null && old('manual')==1))?'display:none':''}}" name="trans_no_auto" type="text" class="form-control trans_no" value="@if($mode=='edit' ||  (old('manual')==null || old('manual')==0)) {{old('trans_no_auto',$journal->trans_no)}} @endif" >
                        <input  id="trans_no_manual" style="{{($mode=='edit' ||  (old('manual')!=null && old('manual')==1))?'':'display:none'}}" name="trans_no_manual" type="text" class="form-control trans_no" value="@if($mode=='edit' ||  (old('manual')!=null && old('manual')==1)) {{old('trans_no_manual',$journal->trans_no)}} @endif" >
                    </div>
                    @error('trans_no_manual')<br/><small class="text-danger">{!!$message!!}</small>@enderror
                    @error('trans_no_auto')<br/><small class="text-danger">{!!$message!!}</small>@enderror
                    @else
                    <input readonly name="trans_no" type="text" class="form-control" value="{{old('trans_no',$journal->trans_no)}}" >
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="trans_date">{{__('Transaction Date')}}:</label>
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
            @if($journal->is_voucher)
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{__('Contact')}}</label>
                    <select name="contact_id" class="form-control select2 contact" data-value="{{old('contact_id', $journal->contact_id)}}"></select>
                    @error('contact_id')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
    @endif
            <div class="col-md-8">
                <div class="form-group">
                    <label>{{__('Description')}}:</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"  rows="1">{{old('description',$journal->description)}}</textarea >
                    @error('description')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
            <div class="col-md">
            </div>
            <div class="col-md">
            </div>
        </div>
    <h4 class="mt-3">{{__('Detail Transaction')}}</h4>
        <div class="container mt-3 mb-3">
            <ul id="detail" class="list-group">
                @php
                $details = $journal->details;
                $row_count = old('detail_length', count($details));
                $row_count = $row_count==0?2:$row_count;
                @endphp
                @for($i=0;$i<$row_count;$i++)
                @php
                if(count($details)==0 || !empty(old('detail'))){
                    $detail = new \App\JournalDetail;
                }else{
                    $detail = $details[$i] ;
                }
                @endphp
                <li class="list-group-item d-item" id="row_{{$i}}" data-index="{{$i}}">
                    <div class="row">
                        <div id="no_{{$i}}" class="col">{{$i+1}}</div>
                        <div class="col text-right"><button type="button" class="btn btn-link btn-sm text-danger btn-remove" data-index="{{$i}}"><i class="fas fa-times"></i></button></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="detail_account_id_{{$i}}" >{{__('Account')}}:</label>
                                <select id="detail_account_id_{{$i}}" data-index="{{$i}}" required data-value="{{old('detail.'.$i.'.account_id', $detail->account_id)}}" name="detail[{{$i}}][account_id]" class="form-control select2 account">

                                </select>
                                @error('detail_account_id_'.$i)<small class="text-danger">{!!$message!!}</small>@enderror

                                @if($mode=='edit')
                                    <input type="hidden" data-index="{{$i}}" id="detail_id_{{$i}}" name="detail[{{$i}}][id]" value="{{$detail->id}}"/>
                                    @endif
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label for="detail_debit_{{$i}}" >{{__('Debit')}}:</label>
                                <input name="detail[{{$i}}][debit]" data-index="{{$i}}" required type="text" id="detail_debit_{{$i}}" class="form-control debit @error('detail.'.$i.'.debit') is-invalid @enderror" value="{{fcurrency(old('detail.'.$i.'.debit', $detail!=null?$detail->debit:''))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                                    @error('detail.'.$i.'.debit')<small class="text-danger">{!!$message!!}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label for="detail_credit_{{$i}}" >{{__('Credit')}}:</label>
                                <input name="detail[{{$i}}][credit]" data-index="{{$i}}" required type="text" id="detail_credit_{{$i}}" class="form-control credit @error('detail.'.$i.'.credit') is-invalid @enderror" value="{{fcurrency(old('detail.'.$i.'.credit', $detail!=null?$detail->credit:'0'))}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                                    @error('detail.'.$i.'.credit')<small class="text-danger">{!!$message!!}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="detail_description_{{$i}}" >{{__('Description')}}:</label>
                                <textarea rows="1" id="detail_description_{{$i}}" name="detail[{{$i}}][description]" data-index="{{$i}}" class="form-control">{{old('detail.'.$i.'.description',$detail->description)}}</textarea>
                                    @error('detail.'.$i.'.description')<small class="text-danger">{!!$message!!}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label for="detail_department_id_{{$i}}" >{{__('Department')}}:</label>
                                <select id="detail_department_id_{{$i}}" data-value="{{old('detail_department_id_'.$i, $detail->department_id)}}" name="detail[{{$i}}][department_id]" class="form-control select2 department">
                                    @foreach($departments as $department)
                                    <option {{old('detail.'.$i.'.department_id', $detail->department_id)==$department->id?'selected':''}} value="{{$department->id}}">{{$department->name}}</option>
                                    @endforeach
                                </select>
                                @error('detail.'.$i.'.department_id')<small class="text-danger">{!!$message!!}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label for="detail_select_tags_{{$i}}" >{{__('Tags')}}:</label>
                                @php
                                $strtags = old('detail.'.$i.'.tags', $detail->tags);
                                $arrtags = explode(',', $strtags);
                                @endphp
                                <input type="hidden" id="detail_tags_{{$i}}" name="detail[{{$i}}][tags]" value="{{$strtags}}" />
                                <select id="detail_select_tags_{{$i}}" data-index="{{$i}}" multiple data-value="{{$strtags}}" class="form-control select2 sortir-select">
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
                                @error('detail.'.$i.'.tags')<small class="text-danger">{!!$message!!}</small>@enderror
                            </div>
                        </div>
                    </div>
                </li>
                @endfor
            </ul>
            <div class="row pr-4 pl-4 mt-2">
                <div class="col">
                    <button id="add-btn" type="button" class="btn btn-secondary btn-sm"><i class="fas fa-plus"></i> Tambah Transaksi</button>
                </div>
                <div class="col text-right">
                    <small class="text-muted">{{__('Total Debit')}}</small><br>
                    <input name="total_debit" type="text" class="total form-control-plaintext text-success text-bold" readonly id="total_debit" value="{{fcurrency(old('total_debit', $journal->total))}}" data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask/>
                    @error('total_debit')<small class="text-danger">{!! $message !!}</small>@enderror
                    <small style="display: none" class="diff-plus text-muted">{{__('Different')}}</small><br>
                    <input style="display: none" type="text" class="diff-plus form-control-plaintext text-warning text-bold" readonly id="diff-plus" data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask/>
                </div>

                <div class="col text-right">
                    <small class="text-muted">{{__('Total Credit')}}</small><br>
                    <input name="total_credit" type="text" class="total form-control-plaintext text-success text-bold" readonly id="total_credit" value="{{fcurrency(old('total_credit', $journal->total))}}" data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask/>
                    @error('total_credit')<small class="text-danger">{!! $message !!}</small>@enderror
                    <small style="display: none" class="diff-min text-muted">{{__('Different')}}</small><br>
                    <input style="display: none" type="text" class="diff-min form-control-plaintext text-warning text-bold" readonly id="diff-min" data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask/>
                    <input type="hidden" id="total" name="total" value="{{old('total', $journal->total)}}" />
                    <input type="hidden" name="is_voucher" value="{{old('is_voucher', $journal->is_voucher)}}" />
                    <input id="detail_length" type="hidden" name="detail_length" value="{{$row_count}}" />
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-sm-6">
            @if($journal->is_voucher)
                @if(company_setting('voucher_approval'))
                    @if($mode=='edit')
                        @if($journal->status=='rejected')
                            <button type="submit" name="status" value="submitted" class="btn btn-primary btn-save" >{{trans('Submit Voucher')}}</button>
                            <button type="submit" name="status" value="draft" class="btn btn-primary btn-save" >{{trans('Save as Draft')}}</button>
                        @else
                            <button type="submit" name="status" value="submitted" class="btn btn-primary btn-save" >{{trans('Save')}}</button>
                        @endif
                    @else
                        <button type="submit" name="status" value="submitted" class="btn btn-primary btn-save" >{{trans('Submit Voucher')}}</button>
                        <button type="submit" name="status" value="draft" class="btn btn-primary btn-save" >{{trans('Save as Draft')}}</button>
                    @endif
                @else
                    <button type="submit" name="status" value="approved" class="btn btn-primary btn-save" >{{trans('Submit Voucher')}}</button>
                @endif
            @else
                <button type="submit" class="btn btn-primary" >{{$mode=='edit'?__('Save'):__('Create').' '.$title_type}}</button>
            @endif
            </div>
            <div class="col-sm-6 text-right">
            <a href="{{route('dcru.index', 'journals')}}" class="btn btn-default" >{{__('Cancel')}}</a>
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
<script src="{{asset('js/select.js')}}"></script>
<script type="text/javascript">
function  init(){
    loadSelect();
    //$("#numbering_id").select2({theme: 'bootstrap4', placeholder:'Select Numbering Format'});
    
    var val = $('#numbering_id').attr('data-value')
    if(val==null|| val==''){
        $('#trans_no_manual').show();
        $('#trans_no_manual').focus();
        $('#trans_no_auto').hide();
        $('#manual').prop('checked', true);
    }
    $('#numbering_id').val(val);
    $('#numbering_id').trigger('change');
    $('input[name=manual]').change(function(){
        var manual = $(this).prop('checked');
        if(manual){
            $('#trans_no_manual').show();
            $('#trans_no_manual').focus();
            $('#trans_no_auto').hide();
            $('#numbering_id').val(null);
            $('#numbering_id').trigger('change');
        }else{
            $('#trans_no_manual').hide();
            $('#trans_no_auto').show();
            var val = $('#numbering_id').attr('data-value')
            $('#numbering_id').val(val);
            $('#numbering_id').trigger('change');
        }
    })

    $('#numbering_id').change(function(){
        var id = $(this).val();
        if(id==null){
            $('#manual').prop('checked', true);
            $('#trans_no_manual').show();
            $('#trans_no_manual').focus();
            $('#trans_no_auto').hide();
            return;
        }else{
            $('#trans_no_manual').hide();
            $('#trans_no_auto').show();
            $('#manual').prop('checked', false);
            $(this).attr('data-value', id)
        }
        $.ajax({
            url: BASE_URL+'/json/numberings/'+id+'/journal',
            method: 'GET',
            success: function(res){
                $('#trans_no_auto').val(res.trans_no)
            }
        })
    })

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

    $('.sortir-select').change(function(e){
        var idx = $(this).attr('data-index');
        $('#detail_tags_'+idx).val($(this).val().join())
    })
    $('.btn-remove').click(function(e){
        e.preventDefault();
        var idx = $(this).attr('data-index');
        if($('#detail_length').val()>2){
            $(this).parent().parent().parent().remove();
        }else{
            $('#detail_debit_'+idx).val('0,00');
            $('#detail_credit_'+idx).val('0,00');
            $('#detail_description_'+idx).val('');
        }
        onchange();
        reindexing();
    })
}
function reindexing(){
    var count = 0;
    $('.d-item').each(function(index){
        count++;
        var i = $(this).attr('data-index');
        $(this).attr('data-index', index);
        $(this).attr('id', 'row_'+index);
        $('#no_'+i).html(index+1);
        $('#no_'+i).attr('id', 'no_'+index);
        $('#detail_id_'+i).attr('data-index', index)
        $('#detail_id_'+i).attr('name', 'detail['+index+'][id]')
        $('#detail_id_'+i).attr('id', 'detail_id_'+index)

        $('#detail_account_id_'+i).attr('data-index', index)
        $('#detail_account_id_'+i).attr('name', 'detail['+index+'][account_id]')
        $('#detail_account_id_'+i).attr('id', 'detail_account_id_'+index)

        $('#detail_debit_'+i).attr('data-index', index)
        $('#detail_debit_'+i).attr('name', 'detail['+index+'][debit]')
        $('#detail_debit_'+i).attr('id', 'detail_debit_'+index)

        $('#detail_credit_'+i).attr('data-index', index)
        $('#detail_credit_'+i).attr('name', 'detail['+index+'][credit]')
        $('#detail_credit_'+i).attr('id', 'detail_credit_'+index)

        $('#detail_description_'+i).attr('data-index', index)
        $('#detail_description_'+i).attr('name', 'detail['+index+'][description]')
        $('#detail_description_'+i).attr('id', 'detail_description_'+index)
        $('#detail_department_id_'+i).attr('data-index', index)
        $('#detail_department_id_'+i).attr('name', 'detail['+index+'][department_id]')
        $('#detail_department_id_'+i).attr('id', 'detail_department_id_'+index)
        $('#detail_tags_'+i).attr('data-index', index)
        $('#detail_tags_'+i).attr('name', 'detail['+index+'][tags]')
        $('#detail_tags_'+i).attr('id', 'detail_tags_'+index)
        $('button[data-index='+i+']').attr('data-index', index)
    });
    $('#detail_length').val(count);
}
function changeIndex(oldIndex, index){
    $('#row_'+oldIndex).attr('id', 'row_'+index);
    $('#row_'+index).attr('data-index', index);
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
        var no = parseInt(idx)+1;
        var row = `
                <li class="list-group-item d-item" id="row_${idx}"  data-index="${idx}">
                    <div class="row">
                        <div id="no_${idx}" class="col">${no}</div>
                        <div class="col text-right"><button type="button" class="btn btn-link btn-sm text-danger btn-remove" data-index="${idx}"><i class="fas fa-times"></i></button></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="detail_account_id_${idx}" >{{__('Account')}}:</label>
                                <select id="detail_account_id_${idx}" required name="detail[${idx}][account_id]" class="form-control select2 account" >

                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label for="detail_debit_${idx}" >{{__('Debit')}}:</label>
                                <input name="detail[${idx}][debit]" data-index="${idx}" value="0,00" required type="text" id="detail_debit_${idx}" class="form-control debit"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label for="detail_credit_${idx}" >{{__('Credit')}}:</label>
                                <input name="detail[${idx}][credit]" data-index="${idx}" value="0,00" required type="text" id="detail_credit_${idx}" class="form-control credit"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="detail_description_${idx}" >{{__('Description')}}:</label>
                                <textarea rows="1"  id="detail_description_${idx}" name="detail[${idx}][description]" data-index="${idx}" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label for="detail_department_id_${idx}" >{{__('Department')}}:</label>
                                <select id="detail_department_id_${idx}" name="detail[${idx}][department_id]" class="form-control select2" >
                                        @foreach($departments as $department)
                                    <option value="{{$department->id}}">{{$department->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label for="detail_select_tags_${idx}" >{{__('Tags')}}:</label>
                                <input type="hidden" id="detail_tags_${idx}" name="detail[${idx}][tags] " />
                                <select id="detail_select_tags_${idx}" data-index="${idx}" multiple class="form-control select2 sortir-select" >
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
                            </div>
                        </div>
                    </div>
                </li>
        `;
        $('#detail').append(row);
        $('#detail_length').val(last_idx+2);
        onchange()
        init();
    });
    $('.debit').change(function(){
        var idx = $(this).attr('data-index');
        var val = parseNumber($(this).val());
        if(val>0){
            $('#detail_credit_'+idx).val('0,00');
        }
        onchange()
    })
    $('.credit').change(function(){
        var idx = $(this).attr('data-index');
        var val = parseNumber($(this).val());
        if(val>0){
            $('#detail_debit_'+idx).val('0,00');
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
    debit = debit.toFixed(2);
    credit = credit.toFixed(2);
    console.log(debit, credit)
    var diff = debit-credit;
    $('#total_debit').val(formatNumber(debit));
    $('#total_credit').val(formatNumber(credit));
    if(diff<0){
        $('.diff-plus').hide();
        $('#diff-min').val(formatNumber(diff));
        $('.diff-min').show();
    }else if(diff>0){
        $('.diff-min').hide();
        $('#diff-plus').val(formatNumber(diff));
        $('.diff-plus').show();
    }else{
        $('.diff-plus').hide();
        $('.diff-min').hide();
    }
    if(debit==credit){
        $('.total').addClass('text-success');
        $('.total').removeClass('text-danger');
        $('#total').val(debit);
        $('.btn-save').prop('disabled', false);
    }else{
        $('.total').addClass('text-danger');
        $('.total').removeClass('text-success');
        $('.btn-save').prop('disabled', true);
    }
}
function sum(selector){
    var total = 0.0;
   $(selector).each(function(index){
      var val = $(this).val();
      var n = parseNumber(val);
      total=total+n;
    });
    return total;
}
function parseNumber(val){
    if(val=='' || val==null)return 0;
    return parseFloat(val.split('.').join('').split(',').join('.'));
}
function formatNumber(val){
    val = val.toString();
    if(val=='' || val==null||val==0)return '0,00';
    if(val.includes('.')){
        return val.split('.').join(',');
    }else{
        return val.split('.').join(',')+',00';
    }
}
</script>
@endpush
