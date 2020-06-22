@isset($breadcrumbs)
@php $countbc = count($breadcrumbs);@endphp
@if($countbc>0)
<ol class="breadcrumb">
  <li class="breadcrumb-item"><a href="/">Home</a></li>
  @foreach($breadcrumbs as $i => $breadcrumb)
  @if($i<$countbc-1)
    <li class="breadcrumb-item">
      <a href="{{$breadcrumb['url']}}">{{__($breadcrumb['label'])}}</a>
    </li>
  @else
    <li class="breadcrumb-item {{$i==$countbc-1?'active':''}}">
      {{__($breadcrumb['label'])}}
    </li>
  @endif
  @endforeach
</ol>
@endif
@endisset