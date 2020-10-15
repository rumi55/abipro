@php
$active_menu='reports';
$breadcrumbs = array(
    ['label'=>trans('Reports')]
);
@endphp
@extends('layouts.app')
@section('title', trans('Reports'))
@section('content')
<div class="row">
  <div class="col-md-3">
    <div class="card">
        <div class="card-body p-0">
          <ul class="nav nav-pills flex-column">
          @foreach($reports as $i=> $r)
            <li class="nav-item">
              <a href="#{{$r['group']}}" class="nav-link {{$i==0?' active':''}}" data-toggle="tab">
                {{__($r['label'])}}
              </a>
            </li>
          @endforeach
          </ul>
        </div>
        <!-- /.card-body -->
        </div>
  </div>
  <div class="col-md-9">

    <div class="tab-content p-0">
      @foreach($reports as $i=> $r)
      <div id="{{$r['group']}}" class="tab-pane {{$i==0?' active':''}}">
        <div class="row d-flex align-items-stretch">
        @foreach($r['reports'] as $report)
          <div class="col-12 col-sm-12 col-md-6 d-flex align-items-stretch">
              <div class="card"style="width:100%">
                  <div class="card-header text-muted border-bottom-0">
                    <h5>{{__($report['title'])}}</h5>
                  </div>
                  <div class="card-body pt-0">
                    <p>{{__($report['description'])}} </p>
                  </div>
                  <div class="card-footer">
                    <div class="text-right">
                      <a href="{{$report['route']}}" class="btn btn-primary">
                        <i class="fas fa-chart-line"></i> {{__('View Report')}}
                    </a>
                    </div>
                  </div>
              </div>
          </div>
        @endforeach
        </div>
      </div>
      @endforeach
    </div>
    </div>
</div>

@endsection
