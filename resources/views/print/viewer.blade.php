
@php
    $active_menu='vouchers';
    $breadcrumbs = array(
        ['label'=>'Vouchers', 'url'=>route('dcru.index', $active_menu)],
        ['label'=>'Detail Voucher', 'url'=>route($active_menu.'.view', $data->id)],
        ['label'=>$title],
    );
@endphp
@extends('layouts.app')
@section('title', trans($title))
@section('content')
<div class="row mb-3">
    <div class="col-md-12 text-right">
        <div class="btn-group">
            <button id="print" type="button" class="btn btn-success"><i class="fas fa-print"></i></button>
            <button id="download" type="button" class="btn btn-success"><i class="fas fa-download"></i></button>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div id="report-container" class="col-md-12">
        <div class="invoice p-5 mb-3 elevation-1 report">
        @include('print._header')
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/0.9.0rc1/jspdf.min.js"></script>
<script type="text/javascript">
$(function () {
    var doc = new jsPDF();
        var specialElementHandlers = {
            '#editor': function (element, renderer) {
            return true;
        }
    };
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
        setTimeout(function(){
        w.focus();
        w.print();
        w.close();
        }, 1000)
    });
    $('#download').click(function(e){
        var url = window.location.href;
        if (url.match(/\?./)) {
            url +='&output=pdf';
        }else{
            url +='?output=pdf';
        }
        window.open(url, '_blank');
        // var css = `
        // <link rel="stylesheet" href="{{asset('css/adminlte.min.css')}}">
        // <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
        // <link rel="stylesheet" href="{{asset('css/report.css')}}">
        // <link rel="stylesheet" media="print" href="{{asset('css/print.css')}}">
        // `;
        // var body = $('.report').html();
        // var html = `
        // <html>
        // <head>${css}</head>
        // <body>${body}</body>
        // </html>
        // `;
        // doc.fromHTML(html, 15, 15, {
        //     'width': 170,
        //         'elementHandlers': specialElementHandlers
        // });
        // doc.save('contoh-file.pdf');

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
