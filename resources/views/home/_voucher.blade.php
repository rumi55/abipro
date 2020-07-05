<div class="card">
  <div class="card-header">
    <h3 class="card-title">{{__('Submitted Voucher')}}</h3>
    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
      <button type="button" class="btn btn-tool" data-card-widget="remove">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body p-0">
    <ul class="products-list product-list-in-card pl-2 pr-2">
    @if(count($vouchers)==0)
    <li class="item">
    <blockquote>{{__('No submitted voucher')}}</blockquote>
    </li>
    @endif
      @foreach($vouchers as $voucher)
      <li class="item">
          <a href="{{route('vouchers.view', $voucher->id)}}" class="product-title">{{$voucher->contact->name}}
            <span class="badge badge-warning float-right">{{fcurrency($voucher->amount)}}</span></a>
          <span class="product-description">
            {{$voucher->description}}
          </span>
      </li>
      @endforeach
      
    </ul>
  </div>
  <!-- /.card-body -->
  <div class="card-footer text-center">
    <a href="{{route('dcru.index', 'vouchers')}}" class="uppercase">{{__('View All Voucher')}}</a>
  </div>
  <!-- /.card-footer -->
</div>