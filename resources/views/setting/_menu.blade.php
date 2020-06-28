<div class="card">
  <div class="card-body p-0">
    <ul class="nav nav-pills flex-column">
      <li class="nav-item">
        <a href="{{route('settings.index')}}" class="nav-link {{$active=='general'?'active':''}}">
          {{__('General')}} 
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('settings.account_mapping')}}" class="nav-link {{$active=='account_mappings'?'active':''}}">
          {{__('Account Mapping')}} 
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