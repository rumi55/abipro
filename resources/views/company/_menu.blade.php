<div class="card">
  <div class="card-body p-0">
    <ul class="nav nav-pills flex-column">
      <li class="nav-item">
        <a href="{{route('company.profile')}}" class="nav-link {{$active=='profile'?'active':''}}">
          {{__('Company')}} 
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('departments.index')}}" class="nav-link {{$active=='departments'?'active':''}}">
          {{__('Department')}} 
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('contacts.index')}}" class="nav-link {{$active=='contacts'?'active':''}}">
        {{__('Contacts')}} 
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('tags.index')}}" class="nav-link {{$active=='tags'?'active':''}}">
        {{__('Tags')}} 
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('journal_types.index')}}" class="nav-link {{$active=='journal_types'?'active':''}}">
        {{__('Journal Type')}} 
        </a>
      </li>
    </ul>
  </div>
</div>