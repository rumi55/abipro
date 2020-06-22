@php 
$active_menu='users'; 
$breadcrumbs = array(
  ['label'=>'Pengguna', 'url'=>route('users.profile')],
    ['label'=>'Hak Akses Pengguna']
);
@endphp
@extends('layouts.app')
@section('title', 'Hak Akses Pengguna')
@section('content')
<div class="row">
    <div class="col-md-3">
            @include('user._menu', ['active'=>'actions'])
    </div>
    <div class="col-md-9">
    @component('components.card'.($user->is_owner?'_form':''), 
    $user->is_owner?['id'=>'action-form','action'=>route('users.actions.save'), 'method'=>'POST']:[]
    )
      <div class="row">
      <div class="col-md-12">
        <div class="input-group mb-2">
          <div class="input-group-prepend">
            <div class="input-group-text">Modul</div>
          </div>
          <select id="group" class="form-control select2" name="group">
            <option value="">Semua Modul</option>
            @foreach($action_groups as $pgroup)
            <option {{$group==$pgroup->group?'selected':''}} value="{{$pgroup->group}}">{{$pgroup->display_group}}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-6">
        
      </div>
      </div>
      <div id="table" class="table_responsive">
        @include('user._action')
      </div>
    @endcomponent
  </div>
</div>

@endsection

@push('css')
<link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush
@push('js')
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
$(function () {
    $(".select2").select2({theme: 'bootstrap4'});

});
</script>
<script type="text/javascript">
$(function(){
  $('#group').change(function(){
    var group = $(this).val();
    $.get('{{route("users.actions")}}', {group:group}, function(res){
      $('#table').html(res);
    });
  });
});
</script>
@endpush