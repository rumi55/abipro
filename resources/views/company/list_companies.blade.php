<div class="card">
    <div class="card-body p-0">
      <ul class="nav nav-pills flex-column">
      @foreach($companies as $i=> $company)
        <li class="nav-item">
          <a href="{{route('companies.index', ['id'=>$company->id])}}" class="nav-link {{$i==0?' active':''}}">
          {{$company->name}}
          @if($company->is_active)<span class="float-right badge badge-success">{{__('Active')}}</span>@endif
          </a>
        </li>
      @endforeach
      </ul>
    </div>
</div>