<div class="card">
  <div class="card-body p-0">
    <ul class="nav nav-pills flex-column">
      <li class="nav-item">
        <a href="{{route('users.profile')}}" class="nav-link {{$active=='profile'?'active':''}}">
          {{__('User Profile')}}
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('users.index')}}" class="nav-link {{$active=='users'?'active':''}}">
          {{__('User List')}}
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('user_groups.index')}}" class="nav-link {{$active=='groups'?'active':''}}">
          {{__('User Group')}}
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('users.actions')}}" class="nav-link {{$active=='actions'?'active':''}}">
          {{__('Authorization')}}
        </a>
      </li>
      <li class="nav-item">
      <a href="{{route('logs.index')}}" class="nav-link {{$active=='logs'?'active':''}}">
          {{__('User Activities')}}
        </a>
      </li>
    </ul>
  </div>
</div>
