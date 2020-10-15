
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
$params = Request::all();
$file = asset(route('reports.'.$report, $params, false));
@endphp
@extends('layouts.viewer')
@section('content-header-right')
<button id="toggle-filter" type="button" class="btn btn-primary"  data-toggle="modal" data-target="#modal-default" ><i class="fas fa-sliders-h"></i></button>
@endsection
@section('title', trans($title))
@section('content')
<div class="modal fade" id="modal-default">
    <form method="GET" action="{{route('reports.view', $report)}}" >
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h4 class="modal-title">Filter</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                @include('report.filter._'.$report.'_filter')
            </div>
            <div class="modal-footer justify-content-between">
                <button type="submit" class="btn btn-primary">Filter</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </form>
  </div>
  <!-- /.modal -->
@endsection
