<div class="card card-primary card-outline">
@if(!empty($title))
  <div class="card-header">
    <h5 class="card-title">{{$title ?? ''}}</h5>
    <div class="card-tools">
      {{$tools ?? ''}}
    </div>
  </div>
@endif
  <div class="card-body">
  {{$slot}}  
  </div>
  {{$footer ?? ''}}
</div>