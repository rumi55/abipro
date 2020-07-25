@php
$active_menu="vouchers";
$type = $transaction->trans_type=='receipt'?'Cash Receipt':'Cash Payment';
$title_type = 'Voucher '.$type;
$breadcrumbs = array(
    ['label'=>__('Voucher'), 'url'=>route('dcru.index','vouchers')],
    ['label'=>($mode=='edit'?__('Edit '.$type):__('New '.$type))],
);
@endphp
@extends('layouts.app')
@section('title', ($mode=='edit'?__('Edit '.$type):__('New '.$type)))
@section('content')
<form method="POST" action="{{$mode=='edit'?route('vouchers.edit.single.update', ['type'=>$transaction->trans_type, 'id'=>$transaction->id]):route('vouchers.create.single.save', ['type'=>$transaction->trans_type])}}">
@csrf
@if($mode=='edit')
@method('PUT')
@endif
<div class="card">
    <div class="card-header">
        <h5 class="card-title">{{$mode=='create'?trans('New Voucher'):trans('Voucher ').'#'.$transaction->trans_no}}</h5>
    </div>
    <div class="card-body pb-1">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="trans_no" >{{__('Transaction Group')}}</label>
                    <input name="auto" value="1" type="hidden">
                    <select name="numbering_id" data-value="{{old('numbering_id', $transaction->numbering_id)}}" class="form-control select2 @error('numbering_id') is-invalid @enderror" style="width:100%">
                        @foreach($numberings as $numbering)
                        <option {{$numbering->id==old('numbering_id', $transaction->numbering_id)?'selected':''}} value="{{$numbering->id}}">{{$numbering->name}}</option>
                        @endforeach
                    </select>
                    @error('numbering_id')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="trans_no" >{{__('Transaction No.')}}</label>
                    <div class="input-group mb-3">
                        <input id="trans_no" {{($mode=='edit' || old('manual', 0)==1)?'':'readonly'}} name="trans_no" type="text" class="form-control" value="@if($mode=='edit' || old('manual', 0)==1) {{old('trans_no',$transaction->trans_no)}} @else [Automatic] @endif" >

                        <div class="input-group-append">
                            <div class="input-group-text">
                            <input id="manual" type="checkbox" {{($mode=='edit' || old('manual', 0)==1)==1?'checked':''}} value="1" name="manual" aria-label="manual"> <label for="manual">Manual</label>
                          </div>
                        </div>
                      </div>
                      @error('trans_no')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="trans_date">{{__('Transaction Date')}}</label>
                    <input id="trans_date" name="trans_date" type="text" class="form-control date @error('trans_date') is-invalid @enderror" value="{{fdate(old('trans_date',$transaction->trans_date))}}" >
                    @error('trans_date')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{$transaction->trans_type=='receipt'?__('Payer'):__('Beneficiary')}}</label>
                    <select name="contact_id" class="form-control select2 contact" data-value="{{old('contact_id', $transaction->contact_id)}}"></select>
                    @error('contact_id')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{__('Account')}}</label>
                    <select name="account_id" data-value="{{old('account_id', $transaction->account_id)}}" class="form-control select2 account">

                    </select>
                    @error('account_id')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{__('Department')}}</label>
                    <select name="department_id" data-value="{{old('department_id', $transaction->department_id)}}" class="form-control select2">
                        @foreach($departments as $department)
                        <option {{old('department_id', $transaction->department_id)==$department->id?'selected':''}} value="{{$department->id}}">{{$department->name}}</option>
                        @endforeach
                    </select>
                    @error('department_id')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{__('Description')}}</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"  rows="1" cols="100">{{old('description',$transaction->description)}}</textarea >
                    @error('description')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
        </div>
        <h4 class="mt-3">{{__('Transaction Detail')}}</h4>
        <div class="container mt-3 mb-3">
            <ul id="detail" class="list-group">
                @php
                $details = $transaction->details;
                $row_count = old('detail_length', count($details));

                $row_count = $row_count==0?1:$row_count;
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
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="detail_description_{{$i}}" >{{__('Description')}}:</label>
                                    <textarea id="detail_description_{{$i}}" name="detail[{{$i}}][description]" data-index="{{$i}}" class="form-control" rows="1" cols="100">{{old('detail.'.$i.'.description',$detail->description)}}</textarea>
                                    @error('detail.'.$i.'.description')<small class="text-danger">{!!$message!!}</small>@enderror
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="detail_account_id_{{$i}}" >{{__('Account')}}:</label>
                                    <select id="detail_account_id_{{$i}}" data-value="{{old('detail.'.$i.'.account_id', $detail->account_id)}}" name="detail[{{$i}}][account_id]" class="form-control select2 account">
                                    </select>
                                    @error('detail.'.$i.'.account_id')<small class="text-danger">{!!$message!!}</small>@enderror
                                    @if($mode=='edit')
                                    <input type="hidden" id="detail_id_{{$i}}" name="detail[{{$i}}][id]" value="{{$detail->id}}"/>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="detail_amount_{{$i}}" >{{__('Total')}}:</label>
                                    <input name="detail[{{$i}}][amount]" data-index="{{$i}}" required type="text" id="detail_amount_{{$i}}" class="form-control amount @error('detail.'.$i.'.amount') is-invalid @enderror" value="{{old('detail.'.$i.'.amount', $detail!=null?$detail->amount:'')}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                                    @error('detail.'.$i.'.amount')<small class="text-danger">{!!$message!!}</small>@enderror
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="detail_department_id_{{$i}}" >{{__('Department')}}:</label>
                                    <select id="detail_department_id_{{$i}}" data-value="{{old('detail.'.$i.'.department_id', $detail->department_id)}}" name="detail[{{$i}}][department_id]" class="form-control select2">
                                        @foreach($departments as $department)
                                        <option value="{{$department->id}}">{{$department->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('detail.'.$i.'.department_id')<small class="text-danger">{!!$message!!}</small>@enderror
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="" >{{__('Tags')}}:</label>
                                    @php
                                    $strtags = old('detail.'.$i.'.tags', $detail->tags);
                                    $arrtags = explode(',', $strtags);
                                    @endphp
                                    <input type="hidden" id="detail_tags_{{$i}}" name="detail[{{$i}}][tags]" value="{{$strtags}}" />
                                    <select id="detail_select_tags_{{$i}}" data-index="{{$i}}" data-value="{{$strtags}}" multiple class="form-control select2 sortir-select">
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
                    <button id="add-btn" type="button" class="btn btn-secondary btn-sm"><i class="fas fa-plus"></i> {{__('Add Transaction')}}</button>
                </div>
                <div class="col text-right">
                    <small class="text-muted">Total</small><br>
                    <input name="amount" type="text" class="total form-control-plaintext text-success text-bold" readonly id="amount" value="{{old('amount', $transaction->amount)}}" data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask/>
                    @error('amount')<small class="text-danger">{!! $message !!}</small>@enderror
                    <input id="detail_length" type="hidden" name="detail_length" value="{{$row_count}}" />
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-sm-6">
            @if($mode=='edit')
                <button id="btn-save" type="submit" name="status" value="submitted" class="btn btn-primary" >{{trans('Save')}}</button>
            @else
                <button id="btn-save" type="submit" name="status" value="submitted" class="btn btn-primary" >{{trans('Submit Voucher')}}</button>
                <button id="btn-save" type="submit" name="status" value="draft" class="btn btn-primary" >{{trans('Save as Draft')}}</button>
            @endif
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('vouchers.index')}}" class="btn btn-default" >{{__('Cancel')}}</a>
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
$(function () {
    init()
    $('input[name=manual]').change(function(){
        var manual = $(this).prop('checked');
        if(manual){
            $('#trans_no').prop('readonly', false)
            $('#trans_no').val("{{($mode=='edit' || old('manual', 0)==1)?old('trans_no',$transaction->trans_no):''}}");
            $('#trans_no').focus();
        }else{
            $('#trans_no').prop('readonly', true)
            $('#trans_no').val('[Automatic]');
        }
    })
    $('.date').daterangepicker({
      timePicker: false,
      singleDatePicker:true,
      autoApply:true,
      locale: {
        format: 'DD-MM-YYYY'
      }
    });
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
        <li class="list-group-item d-item" id="row_${idx}" data-index="${idx}">
            <div class="row">
                <div id="no_${idx}" class="col">${no}</div>
                <div class="col text-right"><button type="button" class="btn btn-link btn-sm text-danger btn-remove" data-index="${idx}"><i class="fas fa-times"></i></button></div>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="detail_description_${idx}" >{{__('Description')}}:</label>
                        <textarea id="detail_description_${idx}" name="detail[${idx}][description]" data-index="${idx}" class="form-control" rows="1" cols="100">{{$detail->description}}</textarea>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="detail_account_id_${idx}" >{{__('Account')}}:</label>
                        <select id="detail_account_id_${idx}" name="detail[${idx}][account_id]" class="form-control select2 account">

                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="detail_amount_${idx}" >{{__('Total')}}:</label>
                        <input name="detail[${idx}][amount]" data-index="${idx}" required type="text" id="detail_amount_${idx}" class="form-control amount @error('detail_amount_'.$i) is-invalid @enderror" value="{{old('detail_amount_'.$i, $detail!=null?$detail->amount:'')}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': ''" data-mask>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="detail_department_id_${idx}" >{{__('Department')}}:</label>
                        <select id="detail_department_id_${idx}" name="detail[${idx}][department_id]" class="form-control select2">
                            @foreach($departments as $department)
                            <option value="{{$department->id}}">{{$department->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="detail_select_tags_${idx}" >Sortir:</label>
                        <input type="hidden" id="detail_tags_${idx}" name="detail[${idx}][tags]" />
                        <select id="detail_select_tags_${idx}" data-index="${idx}" multiple class="form-control select2 sortir-select">
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

    $('.amount').change(function(){
        var idx = $(this).attr('data-index');
        var val = parseNumber($(this).val());
        onchange()
    })

});
function validate(){
  return invalid;
}
function onchange(){
    var amount = sum('.amount');
    $('#amount').val(amount);
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
function  init(){
    loadSelect();
    $('.currency').inputmask({ 'alias': 'currency' })
    $('[data-mask]').inputmask();
    // $(".select2").select2({theme: 'bootstrap4'});

    $('.sortir-select').change(function(e){
        var idx = $(this).attr('data-index');
        $('#detail_tags_'+idx).val($(this).val().join())
    })
    $('.btn-remove').click(function(e){
        e.preventDefault();
        var idx = $(this).attr('data-index');
        var len = $('#detail_length').val();
        if(len>1){
            $(this).parent().parent().parent().remove();
        }else{
            $('#detail_amount_'+idx).val(0);
            $('#detail_description_'+idx).val('');
        }
        reindexing()
        onchange();
        init();
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
        $('#detail_account_id_'+i).attr('data-index', index)
        $('#detail_account_id_'+i).attr('name', 'detail['+index+'][account_id]')
        $('#detail_account_id_'+i).attr('id', 'detail_account_id_'+index)
        $('#detail_amount_'+i).attr('data-index', index)
        $('#detail_amount_'+i).attr('name', 'detail['+index+'][amount]')
        $('#detail_amount_'+i).attr('id', 'detail_amount_id_'+index)
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
        $('#detail_length').val(index+1);
    });
}
</script>
@endpush
