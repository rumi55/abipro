@php
$active_menu='settings';
$mode_title = $mode=='create'?trans('Add'):trans('Edit');
$breadcrumbs = array(
    ['label'=>trans('Settings'), 'url'=>route('settings.index')],
    ['label'=>trans('Templates'), 'url'=>route('report_templates.index')],
    ['label'=>$mode_title.' '.trans('Template')]
);
@endphp
@extends('layouts.app')
@section('title', trans('Numbering'))
@section('content')
<div class="row">
    <div class="col-md-3">
        @include('setting._menu', ['active'=>'templates'])
    </div>
    <div class="col-md-9">
    @component('components.card_form', [
        'id'=>'user-form','action'=>$mode=='create'?route('report_templates.create.save'):route('report_templates.edit.update', $model->id),
        'method'=>$mode=='create'?'POST':'PUT',
        'btn_label'=>$mode=='create'?trans('Create'):trans('Save'),
    ])
      @slot('title')
      <a href="{{route('report_templates.index')}}"><i class="fas fa-chevron-left"></i></a> {{$mode_title}} {{__('Report Template')}}
      @endslot
        <div class="form-group">
            <label for="template_name">{{__('Template Name')}}</label>
            <input type="text" required class="form-control @error('template_name') is-invalid @enderror  @error('template_name') is-invalid @enderror" name="template_name" id="template_name" value="{{old('template_name', $model->template_name)}}">
            @error('template_name') <small class="text-danger">{!! $message !!}</small> @enderror
        </div>
        <div class="form-group">
            <label for="report_name">{{__('Report Type')}}</label>
            <select required class="form-control select2" name="report_name" id="report_name">
                <option {{old('report_name', $model->report_name)=='header'?'selected':''}} value="header">Report Header</option>
                <option {{old('report_name', $model->report_name)=='footer'?'selected':''}} value="footer">Report Footer</option>
                <option {{old('report_name', $model->report_name)=='receipt'?'selected':''}} value="receipt">{{__('Receipt')}}</option>
                <option {{old('report_name', $model->report_name)=='voucher'?'selected':''}} value="voucher">{{__('Voucher')}}</option>
                <option {{old('report_name', $model->report_name)=='journal'?'selected':''}} value="journal">{{__('General Journal')}}</option>
            </select>
            @error('report_name') <small class="text-danger">{!! $message !!}</small> @enderror
        </div>
        <div class="mt-1">
            <div class="icheck-success d-inline">
              <input type="checkbox" name="is_default" value="1" id="is_default"  {{old('is_default', $model->is_default)=='1'?'checked':''}}>
              <label for="is_default">
                Gunakan template ini
              </label>
            </div>
        </div>
        <br/>
        <div class="form-group">
            <label for="template_content">{{__('Template')}}</label>
            <button type="button" class="btn btn-sm btn-info float-right" data-toggle="modal" data-target="#variable-modal">Variable</button>
            <br/>
            <br/>
            <div id="toolbar-container"></div>
            <textarea class="textarea editor" name="template_content" id="editor" >{{old('template_content', $model->template_content)}}</textarea>
            <small class="text-muted"></small>
            @error('template_content') <small class="text-danger">{!! $message !!}</small> @enderror
        </div>
    @endcomponent
  </div>
</div>
<div id="variable-modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Variabel</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div>
                <h5>{{ __('Company Information')}}</h5>
                <table>
                    <tr><td>{company_name}</td><td>:</td><td>{{__('Company Name')}}</td></tr>
                    <tr><td>{company_logo}</td><td>:</td><td>{{__('Company Logo')}}</td></tr>
                    <tr><td>{company_address}</td><td>:</td><td>{{__('Company Address')}}</td></tr>
                    <tr><td>{company_email}</td><td>:</td><td>{{__('Company Email')}}</td></tr>
                    <tr><td>{company_phone}</td><td>:</td><td>{{__('Company Phone')}}</td></tr>
                    <tr><td>{company_fax}</td><td>:</td><td>{{__('Company Fax')}}</td></tr>
                    <tr><td>{company_website}</td><td>:</td><td>{{__('Company Website')}}</td></tr>
                </table>
            </div>
            <div class="mt-3">
                <h5>{{ __('Receipt Information')}}</h5>
                <table>
                    <tr><td>{trans_no}</td><td>:</td><td>{{__('Transaction Number')}}</td></tr>
                    <tr><td>{trans_date}</td><td>:</td><td>{{__('Transaction Date')}}</td></tr>
                    <tr><td>{detail}</td><td>:</td><td>{{__('Transaction Detail')}}</td></tr>
                    <tr><td>{description}</td><td>:</td><td>{{__('Description')}}</td></tr>
                    <tr><td>{amount}</td><td>:</td><td>{{__('Amount')}}</td></tr>
                    <tr><td>{amount_inword}</td><td>:</td><td>{{__('Amount in word')}}</td></tr>
                    <tr><td>{beneficiary}</td><td>:</td><td>{{__('Beneficiary')}}</td></tr>
                </table>
            </div>
            <div class="mt-3">
                <h5>{{ __('Report Component')}}</h5>
                <table>
                    <tr><td>{header}</td><td>:</td><td>{{__('Report Header')}}</td></tr>
                    <tr><td>{pagenum}</td><td>:</td><td>{{__('Page Number')}}</td></tr>
                    <tr><td>{total_page}</td><td>:</td><td>{{__('Total Page')}}</td></tr>
                    <tr><td>{date}</td><td>:</td><td>{{__('Current Date')}}</td></tr>
                    <tr><td>{datetime}</td><td>:</td><td>{{__('Current Date-Time')}}</td></tr>
                </table>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
</div>
@endsection
@push('css')
<link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush
@push('js')
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/ckeditor5/build/ckeditor.js')}}"></script>
<script type="text/javascript">
$(function(){
    ClassicEditor
			.create( document.querySelector( '.editor' ), {

				toolbar: {
					items: [
						'heading',
						'|',
						'bold',
						'italic',
						'underline',
						'|',
						'fontColor',
						'fontSize',
						'|',
						'alignment',
						'bulletedList',
						'numberedList',
						'|',
						'indent',
						'outdent',
						'|',
						'insertTable',
						'horizontalLine',
						'|',
						'code',
						'removeFormat',
						'|',
						'undo',
						'redo'
					]
				},
				language: 'en',
				table: {
					contentToolbar: [
						'tableColumn',
						'tableRow',
						'mergeTableCells',
						'tableCellProperties',
						'tableProperties'
					]
				},
				licenseKey: '',

			} )
			.then( editor => {
				window.editor = editor;








			} )
			.catch( error => {
				console.error( 'Oops, something went wrong!' );
				console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
				console.warn( 'Build id: uhu0jxcau35u-vpr8hyvm6qme' );
				console.error( error );
			} );
})
</script>
@endpush
