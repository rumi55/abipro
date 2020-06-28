  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-purple elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('home')}}" class="brand-link navbar-purple">
      <img src="{{asset('img/logo.png')}}" alt="Abipro Logo" class="brand-image">
      <span class="brand-text">{{ config('app.name', 'Apps75') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{url_image(company('logo'))}}" class="img-circle" alt="logo">
        </div>
        <div class="info">
          <a href="{{route('companies.index')}}"  class="d-block">
          {{company('name')}}
          </a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="{{route('home')}}" class="nav-link {{isset($active_menu) && $active_menu=='home'?'active':''}}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                {{__('Dashboard')}} 
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{route('reports.index')}}" class="nav-link {{isset($active_menu) && $active_menu=='reports'?'active':''}}">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>
                {{__('Report')}}
              </p>
            </a>
          </li>          
          <li class="nav-item">
            <a href="{{asset(route('dcru.index', ['name'=>'vouchers'], false))}}" class="nav-link {{isset($active_menu) && $active_menu=='vouchers'?'active':''}}">
              <i class="nav-icon fas fa-money-check"></i>
              <p>
              {{__('Voucher')}}
              </p>
            </a>
          </li>          
                    
          <li class="nav-item has-treeview {{isset($active_menu) && in_array($active_menu,['sales_invoices', 'sales_quotes','sales_orders'])?'menu-open':''}}">
            <a href="#" class="nav-link {{isset($active_menu) && in_array($active_menu,['sales_invoices', 'sales_quotes','sales_orders'])?'active':''}}">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>
              {{__('Sales')}} 
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{asset(route('dcru.index', ['name'=>'sales_invoices'],false))}}" class="nav-link {{isset($active_menu) && $active_menu=='sales_invoices'?'active':''}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{__('Sales Invoice')}} </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{asset(route('dcru.index', ['name'=>'sales_orders'], false))}}" class="nav-link  {{isset($active_menu) && $active_menu=='sales_orders'?'active':''}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{__('Sales Order')}} </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{asset(route('dcru.index', ['name'=>'sales_quotes'], false))}}" class="nav-link  {{isset($active_menu) && $active_menu=='sales_quotes'?'active':''}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{__('Sales Quote')}} </p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="{{route('dcru.index', 'products')}}" class="nav-link {{isset($active_menu) && $active_menu=='products'?'active':''}}">
              <i class="nav-icon  	fas fa-box"></i>
              <p>
              {{__('Product')}} 
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview {{isset($active_menu) && ($active_menu=='journals' || $active_menu=='accounts')?'menu-open':''}}">
            <a href="#" class="nav-link {{isset($active_menu) && ($active_menu=='journals' || $active_menu=='accounts')?'active':''}}">
              <i class="nav-icon fas fa-book"></i>
              <p>
              {{__('Ledger')}} 
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{asset(route('dcru.index', ['name'=>'journals'],false))}}" class="nav-link {{isset($active_menu) && $active_menu=='journals'?'active':''}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{__('Journal')}} </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{asset(route('dcru.index', ['name'=>'accounts'], false))}}" class="nav-link  {{isset($active_menu) && $active_menu=='accounts'?'active':''}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{__('Account')}} </p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="{{route('company.profile')}}" class="nav-link {{isset($active_menu) && $active_menu=='company'?'active':''}}">
              <i class="nav-icon fas fa-building"></i>
              <p>
              {{__('Company')}} 
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{route('users.index')}}" class="nav-link {{isset($active_menu) && $active_menu=='users'?'active':''}}">
              <i class="nav-icon fas fa-users"></i>
              <p>
              {{__('Users')}} 
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{route('settings.index')}}" class="nav-link {{isset($active_menu) && $active_menu=='settings'?'active':''}}">
              <i class="nav-icon fas fa-cogs"></i>
              <p>
              {{__('Settings')}} 
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>