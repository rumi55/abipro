@php 
$active_menu='users'; 
$title = trans('User Activities');
$breadcrumbs = array(
    ['label'=>trans('Users'), 'url'=>route('users.profile')],
    ['label'=>$title]
);
@endphp
@extends('layouts.app')
@section('title', $title)
@section('content')
<div class="row">
    <div class="col-md-3">
      @include('user._menu', ['active'=>'logs'])
    </div>
    <div class="col-md-9">
    @component('components.card')
    @slot('title') 
      <a href="{{route('logs.index')}}"><i class="fas fa-chevron-left"></i></a> {{__('Detail of Activities')}} 
      @endslot
      <strong>{{__('User')}}</strong>
      <p class="text-muted"><a href="{{route('users.view', $log->created_by)}}">{{$log->user!=null?$log->user->name:'-'}}</a></p>
      <hr>
      <strong>{{__('Module')}}</strong>
      <p class="text-muted">{{$log->action!==null?tt($log->action,'display_group'):'-'}}</p>
      <hr>
      <strong>{{__('Activity')}}</strong>
      <p class="text-muted">{{$log->action!==null?$log->action->display_name:'-'}}</p>
      <hr>
      <strong>{{__('Reference')}}</strong>
      <p class="text-muted">
        <?php
        if($log->action!=null && !empty($log->description)){
          $references = json_decode($log->description);
          if($log->action->name=='create'){
            foreach($references as $key=>$val){
              if($key=='id'){
                $routename = $log->action->group.'.view';$id=$val;
              }else{
              echo '<label>'.trans($key).'</label>: <a href="'.route($routename, $id).'">
              '.(is_numeric($val)?fcurrency($val):$val).'</a><br/>';
              }
            } 
          }elseif($log->action->name=='edit'){
            echo '<div class="row">';
            echo '<div class="col">';
            echo '<b>'.trans('Before').'</b><br>';
            
            foreach($references->before as $key=>$val){
              if($key=='id'){
                $routename = $log->action->group.'.view';$id=$val;
              }else{
              echo '<label>'.trans($key).'</label>: <a href="'.route($routename, $id).'">
              '.($val).'</a><br/>';
              }
            }
            echo '</div>';
            echo '<div class="col">';
            echo '<b>'.trans('After').'</b><br>';
            foreach($references->after as $key=>$val){
              if($key=='id'){
                $routename = $log->action->group.'.view';$id=$val;
              }else{
              echo '<label>'.trans($key).'</label>: <a href="'.route($routename, $id).'">
              '.($val).'</a><br/>';
              }
            }
            echo '</div>';
            echo '</div>';
          }elseif($log->action->name=='delete'){
            foreach($references as $key=>$val){
              if($key=='id'){
                echo '<label>'.trans($key).'</label>: '.$val.'<br/>';
              }
            }
          }elseif($log->action->name=='import'){
            echo '<a target="_blank" href="'.asset($references->url).'">'.$references->name.'</a>';
          }
        }
        ?>
      </p>
      <hr>
      <strong>{{__('Timestamp')}}</strong>
      <p class="text-muted">{{fdatetime($log->created_at)}} ({{hdate($log->created_at)}})</p>
    @endcomponent
  </div>
</div>
@endsection
