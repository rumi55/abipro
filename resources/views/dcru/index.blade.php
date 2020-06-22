@php 
$active_menu=$name; 
$breadcrumbs = array(
    ['label'=>trans($title)],
);
@endphp
@extends('layouts.app')
@section('title', trans($title))
@section('content-header-right')
@php 
  $buttons = '';
  $mainbutton = '';
  $cbuttons = 0;
  if(count($actions)==1){
    $action = $actions[0];
    if($action['type']=='create'){
      $buttons .= has_action($name, 'create')?'<a href="'.route('dcru.create',['name'=>$name]).'" class="btn btn-primary" ><i class="fas fa-plus"></i> '.trans($action['label']).'</a>':'';
      $cbuttons += has_action($name, 'create')?1:0;
    }else{
      $routes = explode('.',$action['route']['name']);
      $buttons.= has_action($routes[0], $routes[1])?'<a href="'.route($action['route']['name'],$action['route']['params']).'" class="btn btn-primary" ><i class="fas fa-plus"></i> '.trans($action['label']).'</a>':'';
      $cbuttons+= has_action($routes[0], $routes[1])?1:0;
    }
  }else{
    foreach($actions as $i=> $action){
      $class=$i==0?'btn btn-primary':'dropdown-item';
      if(isset($action['type']) && $action['type']=='create'){
        if($i==0){
          $mainbutton = has_action($name, 'create')?'<a href="'.route('dcru.create',['name'=>$name]).'" class="'.$class.'" ><i class="fas fa-plus"></i> '.trans($action['label']).'</a>':'';
        }else{
          $buttons .= has_action($name, 'create')?'<a href="'.route('dcru.create',['name'=>$name]).'" class="'.$class.'" ><i class="fas fa-plus"></i> '.trans($action['label']).'</a>':'';
        }        
        $cbuttons += has_action($name, 'create')?1:0;
      }else{
        $routes = explode('.',$action['route']['name']);
        if($i==0){
          $mainbutton= has_action($routes[0], $routes[1])?'<a href="'.route($action['route']['name'],$action['route']['params']).'" class="'.$class.'" ><i class="fas fa-plus"></i> '.trans($action['label']).'</a>':'';
        }else{
          $buttons.= has_action($routes[0], $routes[1])?'<a href="'.route($action['route']['name'],$action['route']['params']).'" class="'.$class.'" ><i class="fas fa-plus"></i> '.trans($action['label']).'</a>':'';
        }
        $cbuttons.= has_action($routes[0], $routes[1])?1:0;
      }
    }
  }
@endphp
@if($cbuttons < 2)
  {!! $buttons !!}
@else
<div class="btn-group">
{!! $mainbutton !!}
  <button type="button" class="btn btn-primary dropdown-toggle dropdown-icon" data-toggle="dropdown"></button>
  <span class="sr-only">Toggle Dropdown</span>
  <div class="dropdown-menu" role="menu">
  {!! $buttons !!}
  </div>
</div>
@endif
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12 text-right">
  
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
    @component('components.card')
      @include('dcru.dtables')
    @endcomponent
  </div>
</div>
@endsection
