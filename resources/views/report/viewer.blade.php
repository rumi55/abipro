
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
$file = 'http://127.0.0.1:8000/reports/balance';
@endphp
@extends('layouts.viewer')
@section('title', trans($title))
@section('content')

@endsection
