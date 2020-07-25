@php
$active_menu="sales_quotes";
$page_title = trans('Sales Quote');
$breadcrumbs = array(
    ['label'=>trans('Sales'), 'url'=>route('dcru.index','sales_invoices')],
    ['label'=>trans('Sales Quote'), 'url'=>route('dcru.index','sales_quotes')],
    ['label'=>($mode=='edit'?trans('Edit'):trans('Create')).' '.$page_title],
);
@endphp
@extends('layouts.app')
@section('title', $page_title)
@section('content')
<form method="POST" id="transaction-form" action="{{$mode=='edit'?route('sales_quotes.edit.update', ['type'=>$transaction->trans_type, 'id'=>$transaction->id]):route('sales_quotes.create.save')}}">
@csrf
@if($mode=='edit')
@method('PUT')
@endif
<div class="card">
    <div class="card-header">
        <h5 class="card-title">{{$mode=='create'?trans('New Sales Quote'):trans('Sales Quote').' #'.$transaction->trans_no}}</h5>
    </div>
    <div class="card-body pb-1">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{__('Customer')}}</label>
                    <select id="customer_id" name="transaction[customer_id]" data-value="{{old('transaction.customer_id', $transaction->customer_id)}}" class="form-control select2"></select>
                    @error('transaction.customer_id')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="trans_date">{{__('Transaction Date')}}</label>
                    <input id="trans_date" name="transaction[trans_date]" type="text" class="form-control date @error('trans_date') is-invalid @enderror" value="{{fdate(old('transaction.trans_date', $transaction->trans_date))}}" >
                    @error('transaction.trans_date')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
            @if($mode=='create')
            <div class="col-md-3">
                <div class="form-group">
                    <label for="trans_no" >{{__('Numbering Format')}}</label>
                    <select data-value="{{old('transaction.numbering_id', $transaction->numbering_id)}}" id="numbering_id" name="transaction[numbering_id]" class="form-control select2 @error('numbering_id') is-invalid @enderror">
                    </select>
                    @error('transaction.numbering_id')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="trans_no" >Nomor</label>
                    <input id="trans_no" readonly name="transaction[trans_no]" type="text" class="form-control" value="@if($mode=='edit') {{old('trans_no',$transaction->trans_no)}} @else [Automatic] @endif" >
                    @error('transaction.trans_no')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
            @endif
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{__('Salesman')}}</label>
                    <select id="salesman_id" name="transaction[salesman_id]" data-value="{{old('transaction.salesman_id', $transaction->salesman_id)}}" class="form-control select2"></select>
                    @error('transaction.salesman_id')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{__('Term')}}</label>
                    <select id="term_id" name="transaction[term_id]" data-value="{{old('transaction.term_id', $transaction->term_id)}}" class="form-control select2"></select>
                    @error('transaction.term_id')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="due_date">{{__('Due Date')}}</label>
                    <input id="due_date" name="transaction[due_date]" type="text" class="form-control date @error('transaction.due_date') is-invalid @enderror" value="{{fdate(old('due_date',$transaction->due_date))}}" >
                    @error('transaction.due_date')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{__('Description')}}</label>
                    <textarea id="description" name="transaction[description]" class="form-control @error('transaction.description') is-invalid @enderror"  rows="1" cols="100">{{old('transaction.description',$transaction->description)}}</textarea >
                    @error('transaction.description')<small class="text-danger">{!!$message!!}</small>@enderror
                </div>
            </div>
        </div>
        <h4 class="mt-3">Rincian Transaksi</h4>
        <div class="container mt-3 mb-3">
            <ul id="detail" class="list-group">
                @php
                $details = $transaction->details;
                $row_count = old('detail_length', count($details));
                $row_count = $row_count==0?1:$row_count;
                @endphp
                @for($i=0;$i<$row_count;$i++)
                @php
                if(count($details)==0){
                    $detail = new \App\JournalDetail;
                }else{
                    $detail = $details[$i] ;
                }
                @endphp
                    <li class="list-group-item d-item" id="row_{{$i}}" data-index="{{$i}}">
                        <div class="row">
                            <div id="no_{{$i}}" class="col">{{$i+1}}</div>
                            <div class="col text-right"><button type="button" class="btn btn-link btn-sm text-danger btn-remove" data-index="{{$i}}"><i class="fas fa-trash-alt"></i></button></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="detail_product_id_{{$i}}" >{{__('Product')}}:</label>
                                    <select id="detail_product_id_{{$i}}"  name="transaction[detail][{{$i}}][product_id]" data-index="{{$i}}" data-value="{{old('transaction.detail.'.$i.'.product_id', $detail->product_id)}}" class="form-control select2 products" style="width:100%" >
                                    </select>
                                    @error('transaction.detail.'.$i.'.product_id')<small class="text-danger">{!!$message!!}</small>@enderror
                                    @if($mode=='edit')
                                    <input type="hidden" id="detail_id_{{$i}}" name="detail_id_{{$i}}" value="{{$detail->id}}"/>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="detail_description_{{$i}}" >{{__('Description')}}:</label>
                                    <textarea id="detail_description_{{$i}}" name="transaction[detail][{{$i}}][description]" data-index="{{$i}}" class="form-control" rows="1" cols="100" >{{old('transaction.detail.'.$i.'.description', $detail->description)}}</textarea>
                                    @error('transaction.detail.'.$i.'.description')<small class="text-danger">{!!$message!!}</small>@enderror
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label for="detail_quantity_{{$i}}" >{{__('Quantity')}}:</label>
                                    <input name="transaction[detail][{{$i}}][quantity]" data-index="{{$i}}" required type="number" id="detail_quantity_{{$i}}" class="form-control number quantity @error('detail_quantity_'.$i) is-invalid @enderror calc" value="{{old('transaction.detail.'.$i.'.quantity', empty($detail->quantity)?1:$detail->quantity)}}" >
                                    @error('transaction.detail.'.$i.'.quantity')<small class="text-danger">{!!$message!!}</small>@enderror
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label for="detail_unit_{{$i}}" >{{__('Unit')}}: </label>
                                    <select id="detail_unit_id_{{$i}}" readonly name="transaction[detail][{{$i}}][unit_id]" data-index="{{$i}}" data-value="{{old('transaction.detail.'.$i.'.unit_id', $detail->unit_id)}}" class="form-control units" style="width:100%" >
                                    </select>
                                    @error('transaction.detail.'.$i.'.unit_id')<small class="text-danger">{!!$message!!}</small>@enderror
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label for="detail_unit_price_{{$i}}" >{{__('Unit Price')}}:</label>
                                    <input name="transaction[detail][{{$i}}][unit_price]" data-index="{{$i}}" required type="text" id="detail_unit_price_{{$i}}" class="form-control unit_price calc number @error('detail_unit_price_'.$i) is-invalid @enderror" value="{{old('transaction.detail.'.$i.'.unit_price', empty($detail->unit_price)?'0,00':$detail->unit_price)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                                    @error('transaction.detail.'.$i.'.unit_price')<small class="text-danger">{!!$message!!}</small>@enderror
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label for="discount_{{$i}}"><input id="discount_{{$i}}" type="checkbox" class="switch_discount" data-index="{{$i}}" /> {{__('Discount')}} <span id="discount_type_{{$i}}"> (%) </span>:</label>
                                    <input style="min-width:80px;display:none" name="transaction[detail][{{$i}}][discount]" data-index="{{$i}}" type="text" id="detail_discount_{{$i}}" class="form-control @error('transaction.detail.'.$i.'.discount') is-invalid @enderror number discount calc" value="{{old('transaction.detail.'.$i.'.discount', empty($detail->discount)?'0,00':$detail->discount)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                                    <input style="min-width:80px" name="transaction[detail][{{$i}}][discount_percent]" data-index="{{$i}}" type="text" id="detail_discount_percent_{{$i}}" class="form-control @error('transaction.detail.'.$i.'.discount_percent') is-invalid @enderror number discount_percent calc" value="{{old('transaction.detail.'.$i.'.discount_percent', empty($detail->discount_percent)?'0,00':$detail->discount)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                                    @error('transaction.detail.'.$i.'.discount')<small class="text-danger">{!!$message!!}</small>@enderror
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label for="detail_tax_id_{{$i}}" >{{__('Tax')}}:</label>
                                    <input name="transaction[detail][{{$i}}][tax]" data-index="{{$i}}" type="hidden" id="detail_tax_{{$i}}" class="form-control calc tax" value="{{old('transaction.detail.'.$i.'.tax', empty($detail->tax)?'0':$detail->tax)}}">
                                    <select id="detail_tax_id_{{$i}}" data-index="{{$i}}" data-value="{{old('transaction.detail.'.$i.'.tax_id', $detail->tax_id)}}" name="transaction[detail][{{$i}}][tax_id]" class="form-control taxes select2" style="width:100%" ></select>
                                    @error('transaction.detail.'.$i.'.tax_id')<small class="text-danger">{!!$message!!}</small>@enderror
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label for="detail_amount_{{$i}}" >{{__('Amount')}}:</label>
                                    <input name="transaction[detail][{{$i}}][amount]" readonly data-index="{{$i}}" required type="text" id="detail_amount_{{$i}}" class="form-control amount number @error('transaction.detail.'.$i.'.amount') is-invalid @enderror" value="{{old('transaction.detail.'.$i.'.amount', empty($detail->amount)?'0,00':$detail->amount)}}"  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                                    @error('transaction.detail.'.$i.'.amount')<small class="text-danger">{!!$message!!}</small>@enderror
                                </div>
                            </div>
                        </div>
                    </li>
                @endfor
            </ul>
            <div class="row pr-4 pl-4 mt-2">
                <div class="col-md-4 col-sm-12">
                    <button id="add-btn" type="button" class="btn btn-secondary btn-sm"><i class="fas fa-plus"></i> Tambah Transaksi</button>
                </div>
                <div class="col-md-8 col-sm-12 text-right">
                    <div class="form-group row">
                        <label class="col-md-8 col-sm-2 col-form-label">{{__('Subtotal')}}</label>
                        <div class="col-md-4 col-sm-10">
                            <input name="transaction[subtotal]" tabindex="-1" type="text" class="subtotal form-control-plaintext text-success number text-bold" readonly id="subtotal" value="{{old('transaction.subtotal', empty($transaction->subtotal)?'0,00':$transaction->subtotal)}}" data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask/>
                            @error('transaction.subtotal')<small class="text-danger">{!! $message !!}</small>@enderror
                        </div>
                    </div>
                    <div id="tax-container" class="form-group row">
                        <label class="col-md-8 col-sm-2 col-form-label">{{__('Tax')}}</label>
                        <div class="col-md-4 col-sm-10">
                            <input name="transaction[tax]" tabindex="-1" type="text" class="tax number form-control-plaintext text-success text-bold" readonly id="tax" value="{{old('transaction.tax', empty($transaction->tax)?'0,00':$transaction->tax)}}" data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask/>
                            @error('transaction.tax')<small class="text-danger">{!! $message !!}</small>@enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-8 col-sm-2 col-form-label">{{__('Total')}}</label>
                        <div class="col-md-4 col-sm-10">
                            <input name="transaction[total]" tabindex="-1" type="text" class="total number form-control-plaintext text-success text-bold" readonly id="total" value="{{old('transaction.total', empty($transaction->total)?'0,00':$transaction->total)}}" data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask/>
                            @error('transaction.total')<small class="text-danger">{!! $message !!}</small>@enderror
                        </div>
                    </div>
                    <input id="detail_length" type="hidden" name="detail_length" value="{{$row_count}}" />
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-sm-6">
                <button id="btn-save" type="submit" class="btn btn-primary" >{{$mode=='edit'?__('Save'):__('Create')}}</button>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('dcru.index', 'journals')}}" class="btn btn-default" >Batal</a>
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


$(function () {
    load();
    init()

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
        addRow();
    });
    $('#numbering_id').change(function(e){
        var val = $(this).val();

        if(val=='' || val==null){
            $('#trans_no').prop('readonly', false);
            $('#trans_no').val('');
            $('#trans_no').focus();
        }else{
            $('#trans_no').prop('readonly', true);
            $('#trans_no').val('[Automatic]');
        }
    });
    $('#trans_date').on('change', function (e) {
        $('#term_id').trigger('change');
    })
    $('#term_id').on('select2:select', function (e) {
        var data = e.params.data;
        if(data!=null){
            var date = moment($('#trans_date').val(), "DD-MM-YYYY");
            var dueDate = date.add(data.period-1, 'days');
            $('#due_date').val(dueDate.format("DD-MM-YYYY"))
        }
    })


});
function validate(){
  return invalid;
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
function sumTax(){
    var tax =0;
    $('.tax').each(function(index){
        var i = $(this).attr('data-index');
        var amount = parseNumber($('#detail_amount_'+i).val());
        var percent = $(this).val();
        tax += percent/100*amount;
    })
    return tax;
}
function parseNumber(val){
    if(val=='' || val==null)return 0;
    //return parseInt(val.split('.').join(''));
    return parseFloat(val.split('.').join('').split(',').join('.'));
}
function parseDec(val){
    if(val=='' || val==null)return 0;
    return parseFloat(val.split('.').join('').split(',').join('.'));
}
function  init(){
    $(".select2").select2({theme: 'bootstrap4'});
    setup();
}
function  setup(){
    $('[data-mask]').inputmask();
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
        setup();
        if(len==1){
            setSelect2('#detail_product_id_0', products)
            setSelect2('#detail_tax_id_0', taxes)
        }
    });
    $('.calc').on('change', function (e) {
        var index = $(this).attr('data-index');
        sumItem(index)
    })
    $('.products').on('select2:select', function (e) {
        var data = e.params.data;
        var index = $('#'+e.params.data.element.parentNode.id).attr('data-index');
        $('#detail_unit_price_'+index).val(data.unit_price+',00')
        $('#detail_unit_id_'+index).val(data.unit_id)
        $('#detail_unit_id_'+index).trigger('change')
        sumItem(index);
    })
    $('.taxes').on('select2:select', function(e){
        var data = e.params.data;
        var index = $('#'+e.params.data.element.parentNode.id).attr('data-index');
        $('#detail_tax_'+index).val(data.percentage);
        sumItem(index);
    })
    $('.switch_discount').change(function(){
        var check = $(this).prop('checked');
        var i = $(this).attr('data-index');
        if(check){
            $('#detail_discount_percent_'+i).hide()
            $('#detail_discount_'+i).show()
            $('#detail_discount_'+i).focus()
            $('#discount_type_'+i).html('(Rp)');
        }else{
            $('#detail_discount_'+i).hide()
            $('#detail_discount_percent_'+i).show()
            $('#detail_discount_percent_'+i).focus()
            $('#discount_type_'+i).html('(%)');
        }
    })
    $('.discount').change(function(){
        var index = $(this).attr('data-index');
        var val = parseDec($(this).val());
        var qty = parseNumber($('#detail_quantity_'+index).val());
        var unitPrice = parseNumber($('#detail_unit_price_'+index).val());
        var total = qty*unitPrice;
        if(val>total){
            val = total;
            $(this).val(total.toFixed(2).split('.').join(','));
        }
        var percent = val/total*100;
        percent = percent.toFixed(2)
        percent = percent.split('.').join(',');
        $('#detail_discount_percent_'+index).val(percent);
        sumItem(index)
    })
    $('.discount_percent').change(function(){
        var index = $(this).attr('data-index');
        var percent = parseDec($(this).val());
        if(percent>100){
            percent = 100;
            $(this).val('100,00');
        }
        var qty = parseNumber($('#detail_quantity_'+index).val());
        var unitPrice = parseNumber($('#detail_unit_price_'+index).val());
        var total = qty*unitPrice;
        var val = percent/100*total;
        val = val.toFixed(2)
        val = val.split('.').join(',');
        $('#detail_discount_'+index).val(val);
        sumItem(index)
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
        $('#detail_product_id_'+i).attr('data-index', index)
        $('#detail_product_id_'+i).attr('name', 'transaction[detail]['+index+'][product_id]')
        $('#detail_product_id_'+i).attr('id', 'detail_product_id_'+index)
        $('#detail_description_'+i).attr('data-index', index)
        $('#detail_description_'+i).attr('name', 'transaction[detail]['+index+'][description]')
        $('#detail_description_'+i).attr('id', 'detail_description_'+index)
        $('#detail_quantity_'+i).attr('data-index', index)
        $('#detail_quantity_'+i).attr('name', 'transaction[detail]['+index+'][quantity]')
        $('#detail_quantity_'+i).attr('id', 'detail_quantity_'+index)
        $('#detail_unit_id_'+i).attr('data-index', index)
        $('#detail_unit_id_'+i).attr('name', 'transaction[detail]['+index+'][unit_id]')
        $('#detail_unit_id_'+i).attr('id', 'detail_unit_id_'+index)
        $('#detail_unit_price_'+i).attr('data-index', index)
        $('#detail_unit_price_'+i).attr('name', 'transaction[detail]['+index+'][unit_price]')
        $('#detail_unit_price_'+i).attr('id', 'detail_unit_price_'+index)
        $('#detail_discount_'+i).attr('data-index', index)
        $('#detail_discount_'+i).attr('name', 'transaction[detail]['+index+'][discount]')
        $('#detail_discount_'+i).attr('id', 'detail_discount_'+index)
        $('#detail_discount_percent_'+i).attr('data-index', index)
        $('#detail_discount_percent_'+i).attr('name', 'transaction[detail]['+index+'][discount_percent]')
        $('#detail_discount_percent_'+i).attr('id', 'detail_discount_percent_'+index)
        $('#discount_'+i).attr('data-index', index)
        $('#discount_'+i).attr('id', 'discount_'+index)
        $('#discount_type_'+i).attr('id', 'discount_type_'+index)
        $('#detail_tax_'+i).attr('data-index', index)
        $('#detail_tax_'+i).attr('name', 'transaction[detail]['+index+'][tax]')
        $('#detail_tax_'+i).attr('id', 'detail_tax_'+index)
        $('#detail_tax_id_'+i).attr('data-index', index)
        $('#detail_tax_id_'+i).attr('name', 'transaction[detail]['+index+'][tax_id]')
        $('#detail_tax_id_'+i).attr('id', 'detail_tax_id_'+index)
        $('#detail_amount_'+i).attr('data-index', index)
        $('#detail_amount_'+i).attr('name', 'transaction[detail]['+index+'][amount]')
        $('#detail_amount_'+i).attr('id', 'detail_amount_'+index)
        $('button[data-index='+i+']').attr('data-index', index)
        $('#detail_length').val(index+1);
    });
}

function addRow(){
    var idx = $('#detail_length').val();
    var last_idx = parseInt(idx)-1;
    var no = parseInt(idx)+1;
    var row = `
    <li class="list-group-item d-item" id="row_${idx}" data-index="${idx}">
                        <div class="row">
                            <div id="no_${idx}" class="col">${no}</div>
                            <div class="col text-right"><button type="button" class="btn btn-link btn-sm text-danger btn-remove" data-index="${idx}"><i class="fas fa-trash-alt"></i></button></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="detail_product_id_${idx}" >{{__('Product')}}:</label>
                                    <select id="detail_product_id_${idx}"  name="transaction[detail][${idx}][product_id]" data-index="${idx}" data-value="" class="form-control select2 products" style="width:100%" >
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="detail_description_${idx}" >{{__('Description')}}:</label>
                                    <textarea id="detail_description_${idx}" name="transaction[detail][${idx}][description]" data-index="${idx}" class="form-control" rows="1" cols="100" ></textarea>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label for="detail_quantity_${idx}" >{{__('Quantity')}}:</label>
                                    <input name="transaction[detail][${idx}][quantity]" data-index="${idx}" required type="number" id="detail_quantity_${idx}" class="form-control number quantity @error('detail_quantity_'.$i) is-invalid @enderror calc" value="1" >
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label for="detail_unit_${idx}" >{{__('Unit')}}: </label>
                                    <select id="detail_unit_id_${idx}" readonly name="transaction[detail][${idx}][unit_id]" data-index="${idx}" data-value="" class="form-control units" style="width:100%" >
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label for="detail_unit_price_${idx}" >{{__('Unit Price')}}:</label>
                                    <input name="transaction[detail][${idx}][unit_price]" value="0,00" data-index="${idx}" required type="text" id="detail_unit_price_${idx}" class="form-control unit_price calc " data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label for="discount_${idx}"><input id="discount_${idx}" type="checkbox" class="switch_discount" data-index="${idx}" /> {{__('Discount')}} <span id="discount_type_${idx}"> (%) </span>:</label>
                                    <input style="min-width:80px;display:none" name="transaction[detail][${idx}][discount]" data-index="${idx}" type="text" id="detail_discount_${idx}" class="form-control @error('transaction.detail.'.$i.'.discount') is-invalid @enderror number discount calc" value=""  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                                    <input style="min-width:80px" name="transaction[detail][${idx}][discount_percent]" data-index="${idx}" type="text" id="detail_discount_percent_${idx}" class="form-control @error('transaction.detail.'.$i.'.discount_percent') is-invalid @enderror number discount_percent calc" value=""  data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label for="detail_tax_id_${idx}" >{{__('Tax')}}:</label>
                                    <input name="transaction[detail][${idx}][tax]" data-index="${idx}" type="hidden" id="detail_tax_${idx}" class="form-control calc tax" value="">
                                    <select id="detail_tax_id_${idx}" data-index="${idx}" data-value="" name="transaction[detail][${idx}][tax_id]" class="form-control select2" style="width:100%" ></select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label for="detail_amount_${idx}" >{{__('Amount')}}:</label>
                                    <input name="transaction[detail][${idx}][amount]" value="0,00" readonly data-index="${idx}" required type="text" id="detail_amount_${idx}" class="form-control amount " data-inputmask="'alias':'decimal', 'groupSeparator': '.', 'radixPoint':',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': ''" data-mask>
                                </div>
                            </div>
                        </div>
                    </li>
    `;
    $('#detail').append(row);
    $('#detail_length').val(last_idx+2);
    setSelect2('#detail_product_id_'+idx, products)
    setSelect2('#detail_tax_id_'+idx, taxes)
    setSelect('#detail_unit_id_'+idx, units)
    setup();
}
function sumItem(index){
    var discount = parseDec($('#detail_discount_'+index).val());
    var qty = $('#detail_quantity_'+index).val();
    var unitPrice = parseNumber($('#detail_unit_price_'+index).val());
    var total = qty*unitPrice-discount;

    $('#detail_amount_'+index).val(total+',00');
    var amount = sum('.amount');
    var tax = sumTax();
    if(tax==0){
        $('#tax-container').hide()
    }else{
        $('#tax-container').show()
    }
    $('#subtotal').val(amount+',00');
    $('#total').val((amount-tax)+',00');
    $('#tax').val(tax+',00')
}
var products = [];
var taxes = [];
var units = [];
function load(){
    $.ajax({
        url: "{{route('json.output', 'products')}}",
        dataType: 'json',
        success: function(res){
            products = $.map(res.data, function (item) {
                return {
                    id: item.id,
                    text: '('+item.custom_id+') '+item.name,
                    unit_price: item.sale_price,
                    unit_id: item.unit.id,
                    unit_name: item.unit.name,
                    main_desc: item.name,
                    left_desc: item.custom_id,
                    right_desc: item.category.name
                }
            })
            $('.products').each(function(index){
                setSelect2('#detail_product_id_'+index,products)
            })
        }
    });
    $.ajax({
        url: "{{route('json.output', 'taxes')}}",
        dataType: 'json',
        success: function(res){
            taxes = $.map(res.data, function (item) {
                return {
                    id: item.id,
                    text: item.name,
                    percentage: item.percentage,
                    main_desc: item.name,
                    left_desc: '',
                    right_desc: ''
                }
            })
            $('.taxes').each(function(index){
                setSelect2('#detail_tax_id_'+index,taxes)
            })
        }
    });
    $.ajax({
        url: "{{route('json.output', 'contacts')}}",
        data:{'type':'customer'},
        dataType: 'json',
        success: function(res){
            var contacts = $.map(res.data, function (item) {
                return {
                    id: item.id,
                    text: item.name,
                    main_desc: item.name,
                    left_desc: item.custom_id,
                    right_desc: item.email
                }
            })
            setSelect2('#customer_id',contacts)
        }
    });
    $.ajax({
        url: "{{route('json.output', 'contacts')}}",
        data:{'type':'employee'},
        dataType: 'json',
        success: function(res){
            var contacts = $.map(res.data, function (item) {
                return {
                    id: item.id,
                    text: item.name,
                    main_desc: item.name,
                    left_desc: item.custom_id,
                    right_desc: item.email
                }
            })
            setSelect2('#salesman_id',contacts)
        }
    });

    $.ajax({
        url: "{{route('json.output', 'numberings')}}",
        dataType: 'json',
        data:{type:'sales'},
        success: function(res){
            var n = $.map(res, function (item) {
                return {
                    id: item.id,
                    text: item.name,
                    main_desc: item.name,
                    left_desc: '',
                    right_desc: ''
                }
            })
            setSelect2('#numbering_id',n)
            $('#numbering_id').trigger('change');
        }
    });
    $.ajax({
        url: "{{route('json.output', 'terms')}}",
        dataType: 'json',
        success: function(res){
            var n = $.map(res, function (item) {
                return {
                    id: item.id,
                    text: item.name,
                    period: item.period,
                    main_desc: item.name,
                    left_desc: '',
                    right_desc: ''
                }
            })
            setSelect2('#term_id',n)
            $('#term_id').trigger('change');
        }
    });
    $.ajax({
        url: "{{route('json.output', 'units')}}",
        dataType: 'json',
        success: function(res){
            units = res;
            $('.units').each(function(index){
                setSelect('#'+$(this).attr('id'), units)
            })
        }
    });
}

function setSelect(selector, data){
    var val = $(selector).attr('data-value');
    for(var i=0;i<data.length;i++){
        var dt = data[i];
        $(selector).append('<option '+(dt.id==val?'selected':'')+' value="'+(dt.id)+'">'+(dt.name)+'</option>');
    }
}
function formatResults(item) {
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
