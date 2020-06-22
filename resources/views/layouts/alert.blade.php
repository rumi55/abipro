@if ($message = Session::get('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h4><i class="icon fa fa-ban"></i> {{__('Error')}}!</h4>
    {{ $message }}
</div>
@endif
@if ($message = Session::get('info'))
<div class="alert alert-info alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
  <h4><i class="icon fa fa-info"></i> {{__('Info')}}!</h4>
  {{ $message }}
</div>
@endif
@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
  <h4><i class="icon fa fa-warning"></i> {{__('Warning')}}!</h4>
  {{ $message }}
</div>
@endif
@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
  <h4><i class="icon fa fa-check"></i> {{__('Success')}}!</h4>
  {{ $message }}
</div>
@endif
