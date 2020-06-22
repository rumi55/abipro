@php $active_menu ='home'; @endphp
@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            @include('home._chart')
        </div>    
    </div>    
</div>
@endsection
@push('js')
@push('js')
<script src="{{asset('plugins/chart.js/Chart.min.js')}}"></script>
<script type="text/javascript">

</script>
@endpush
