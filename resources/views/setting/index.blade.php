@php
$active_menu='settings';
$breadcrumbs = array(
['label'=>'Settings']
);
@endphp
@extends('layouts.app')
@section('title', trans('Settings'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                @include('setting._menu', ['active'=>'general'])
            </div>
            <div class="col-md-9">
                <form action="{{ route('settings.index.save') }}" method="POST">
                    <div class="card">
                        <div class="card-body">
                            @csrf
                            <h4>Voucher</h4>
                            <div class="form-group row">
                                <label for="voucher_approval" class="col-sm-2 col-form-label">{{ __('Approval') }}</label>
                                <div class="col-sm-10 pt-2">
                                    <div class="icheck-primary d-inline">
                                        <input type="radio" value="1" id="voucher_approval_yes" name="voucher_approval" @if (company_setting('voucher_approval')==1) checked @endif>
                                        <label for="voucher_approval_yes">
                                            {{__('Yes')}}
                                        </label>
                                    </div>

                                    <div class="icheck-primary d-inline">
                                        <input type="radio" value="0" id="voucher_approval_no" name="voucher_approval" @if (company_setting('voucher_approval')==0) checked @endif>
                                        <label for="voucher_approval_no">
                                            {{__('No')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-primary">{{__('Save')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
@endpush
@push('js')
    <script src="{{ asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <script>
        $(function() {
            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });
            $("input[name='voucher_approval']").change(function() {
                var val = $(this).val();

                if (val==1 && $("#on_create").prop("checked")) {
                    $("#manual").prop("checked", true)
                } else if (val == 0 && $("#on_approve").prop("checked")) {
                    $("#manual").prop("checked", true)
                }
                if(val==1){
                    $("#on_create").prop("disabled", true)
                    $("#on_approve").prop("disabled", false)
                }else{
                    $("#on_create").prop("disabled", false)
                    $("#on_approve").prop("disabled", true)
                }
            });
            // $("#voucher_approval").on('switchChange.bootstrapSwitch', function(event, state) {
            //     if (state && $("#on_create").prop("checked")) {
            //         $("#on_approve").prop("checked", state)
            //     } else if (state == false && $("#on_approve").prop("checked")) {
            //         $("#on_approve").prop("checked", state)
            //     }
            //     $("#on_create").prop("disabled", state)
            //     $("#on_approve").prop("disabled", !state)
            // });

        })

    </script>
@endpush
