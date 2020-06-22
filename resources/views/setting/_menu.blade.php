<div class="card">
  <div class="card-body p-0">
    <ul class="nav nav-pills flex-column">
      <li class="nav-item">
        <a href="{{route('company.profile')}}" class="nav-link {{$active=='profile'?'active':''}}">
          {{__('General')}} 
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('numberings.index')}}" class="nav-link {{$active=='numberings'?'active':''}}">
          {{__('Numberings')}} 
        </a>
      </li>
    </ul>
  </div>
</div>