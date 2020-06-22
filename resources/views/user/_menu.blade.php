<div class="card">
  <div class="card-body p-0">
    <ul class="nav nav-pills flex-column">
      <li class="nav-item">
        <a href="{{route('users.profile')}}" class="nav-link {{$active=='profile'?'active':''}}">
          Profil Pengguna
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('users.index')}}" class="nav-link {{$active=='users'?'active':''}}">
          Daftar Pengguna
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('user_groups.index')}}" class="nav-link {{$active=='groups'?'active':''}}">
          Grup Pengguna
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('users.actions')}}" class="nav-link {{$active=='actions'?'active':''}}">
          Hak Akses Pengguna
        </a>
      </li>
      <li class="nav-item">
      <a href="{{route('logs.index')}}" class="nav-link {{$active=='logs'?'active':''}}">
          Aktivitas Pengguna
        </a>
      </li>
    </ul>
  </div>
</div>
