    <div class="card">
      <div class="card-header">
        <h3 class="card-title">{{$company->name}} @if($company->is_active)<span class="badge badge-success">{{__('Active')}}</span>@endif</h3>
        <div class="card-tools">
        <a href="{{route('company.profile.edit')}}" class="btn btn-tool" title="Edit Profil"><i class="fa fa-edit"></i></a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3 col-sm-12">
            <div class="text-center mb-3">
              <img class="profile-user-img img-fluid img-circle" src="{{url_image($company->logo)}}" alt="Logo">
            </div>
            @if(!$company->is_active)
            <form action="{{route('companies.active', $company->id)}}" method="POST" class="mb-2">
            @method('PUT')
            @csrf
              <button type="submit" class="btn btn-success btn-block">{{__('Activate').' '.__('Company')}}</button>
            </form>
            @endif
            @if($user->is_owner && $company->is_active)
              <a href="{{route('companies.export')}}" class="btn btn-info btn-block">{{__('Export Data')}}</a>
              <a href="{{route('companies.import')}}" class="btn btn-info btn-block">{{__('Import Data')}}</a>
              <a href="{{route('companies.transfer')}}" class="btn btn-info btn-block">{{__('Transfer Data')}}</a>
              <a href="{{route('convert.index')}}" class="btn btn-info btn-block">{{__('Convert Abipro Desktop')}}</a>
            @endif
            @if($user->is_owner && !$company->is_active)
              <a href="{{route('companies.delete.confirm', $company->id)}}" class="btn btn-danger btn-block">{{__('Delete').' '.__('Company')}}</a>
            @endif
          </div>
          <div class="col-md-9 col-sm-12">
              <strong><i class="fas fa-map-marker-alt mr-1"></i> Alamat</strong>
              <p class="text-muted">
                {{empty($company->address)?'-':$company->address}}
              </p>
              <hr>
              <strong><i class="fas fa-map-marker-alt mr-1"></i> Alamat Pengiriman</strong>
              <p class="text-muted">
                {{empty($company->shipping_address)?'-':$company->shipping_address}}
              </p>
              <hr>
              <strong><i class="fas fa-phone mr-1"></i> Telepon</strong>
              <p class="text-muted">{{empty($company->phone)?'-':$company->phone}}</p>
              <hr>
              <strong><i class="fas fa-fax mr-1"></i> Fax</strong>
              <p class="text-muted">{{empty($company->fax)?'-':$company->fax}}</p>
              <hr>
              <strong><i class="far fa-envelope mr-1"></i> Email</strong>
              <p class="text-muted">{{empty($company->email)?'-':$company->email}}</p>
              <hr>
              <strong><i class="far fa-globe mr-1"></i> Website</strong>
              <p class="text-muted">{{empty($company->website)?'-':$company->website}}</p>
            </div>
        </div>
      </div>
    </div>