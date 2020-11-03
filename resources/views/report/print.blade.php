
@php
if($group=='vouchers') {
    $active_menu='vouchers';
    $breadcrumbs = array(
        ['label'=>trans('Vouchers'), 'url'=>route('dcru.index', $active_menu)],
        ['label'=>'Detail '.trans('Voucher'), 'url'=>route($active_menu.'.view', $id)],
        ['label'=>trans($title)],
    );
}else if($group=='journals') {
    $active_menu='journals';
    $breadcrumbs = array(
        ['label'=>trans('Journals'), 'url'=>route('dcru.index', $active_menu)],
        ['label'=>'Detail '.trans('Journal'), 'url'=>route($active_menu.'.view', $id)],
        ['label'=>trans($title)],
    );
}else{
    $active_menu='reports';
    $breadcrumbs = array(
        ['label'=>trans('Report'), 'url'=>route('reports.index')],
        ['label'=>$title],
    );
}
@endphp
@extends('layouts.viewer')
@section('title', trans($title))
@section('content')
@endsection
