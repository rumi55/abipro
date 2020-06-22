@extends('layouts.app')
@section('title', 'Pengguna')
@section('content')
<div class="row">
  <div class="col-lg-12">
    @component('components.card_form', ['action'=>route('user.password.save')])
      @slot('title') Ganti Password @endslot
      
    @endcomponent
  </div>
</div>
@endsection

@push('css')

@endpush

@push('js')
<script type="text/javascript">
$(function(){
  
});
</script>
@endpush