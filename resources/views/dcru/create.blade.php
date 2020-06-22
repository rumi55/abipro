@extends('layouts.app')
@section('title', $title)
@section('content')
<div class="row">
  <div class="col-lg-12">
      @include('dcru.form')
  </div>
</div>

@endsection
@push('css')

@endpush

@push('js')

@endpush