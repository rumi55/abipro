
@php
if($report=='print_journal') {
    $active_menu=$data->is_voucher==0?'journals':'vouchers'; 
    $breadcrumbs = array(
        ['label'=>trans($title), 'url'=>route('dcru.index', $active_menu)],
        ['label'=>'Detail '.trans($data->is_voucher==0?'Jurnal':'Voucher'), 'url'=>route($active_menu.'.view', $data->id)],
        ['label'=>trans('Cetak') .' '.trans($data->is_voucher==0?'Journal':'Voucher')],
    );
}else{
    $active_menu='reports'; 
    $breadcrumbs = array(
        ['label'=>trans('Report'), 'url'=>route('reports.index')],
        ['label'=>$title],
    );
}
@endphp
@extends('layouts.app')
@section('title', trans($title))
@section('content')
<div class="row mb-3">
    <div class="col-md-12 text-right">
        <div class="btn-group">
            <button id="print" type="button" class="btn btn-success"><i class="fas fa-print"></i></button>
            <button id="download" type="button" class="btn btn-success"><i class="fas fa-download"></i></button>
            @if(!in_array($report, ['print_journal', 'print_receipt']))
            <button id="toggle-filter" type="button" class="btn btn-success" data-toggle="collapse" data-target="#filter" aria-expanded="false" aria-controls="filter" ><i class="fas fa-sliders-h"></i></button>
            @endif
        </div>
    </div>
</div>
<div id="filter"  class="row collapse">
    <div class="col-md-12">
    @if(!in_array($report, ['print_journal', 'print_receipt']))
        @include('report.filter._'.$report.'_filter')
    @endif
    </div>
</div>

<div class="row mb-3">
    <div id="report-container" class="col-md-12">
        <div class="invoice p-5 mb-3 elevation-1 report">
        @include('report._header')   
        <div class="table-responsive">
            @include($view)   
        </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('css/report.css')}}">
<link rel="stylesheet" media="print" href="{{asset('css/print.css')}}">
@endpush
@push('js')
<script type="text/javascript">
$(function () {
    $('#print').on('click',function(e){
        var w = window.open();
        var css = `
        <link rel="stylesheet" href="{{asset('css/adminlte.min.css')}}">
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('css/report.css')}}">
        <link rel="stylesheet" media="print" href="{{asset('css/print.css')}}">
        `;
        var body = $('.report').html();
        var html = `
        <html>
        <head>${css}</head>
        <body>${body}</body>
        </html>
        `;
        w.document.write(html);
        w.document.close();
        w.focus();
        w.print();
        w.close();
    });
    $('#download').click(function(e){
        var url = window.location.href;
        if (url.match(/\?./)) {
            url +='&output=pdf';
        }else{
            url +='?output=pdf';
        }
        window.open(url, '_blank');
    });
    $('#filter').on('hide.bs.collapse', function () {
      $('#toggle-filter').removeClass('active')
    })
    $('#filter').on('show.bs.collapse', function () {
      $('#toggle-filter').addClass('active')
    })
});

</script>
@endpush