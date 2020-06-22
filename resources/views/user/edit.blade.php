@extends('layouts.app')
@section('title', 'Pengguna')
@section('content')
<div class="row">
  <div class="col-lg-12">
    @component('components.card_form', ['action'=>route('user.edit.update')])
      @slot('title') Edit Profil@endslot
      
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