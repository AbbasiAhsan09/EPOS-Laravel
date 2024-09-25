<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="/" >
        @if (!empty($currenConfig->logo))
        <img src="{{asset("images/logo/$currenConfig->logo")}}" class="navbar-brand-img h-100" alt="main_logo">
        @endif
        @if (empty($currenConfig->logo))
        <img src="{{asset("images/logo.png")}}" class="navbar-brand-img h-100" alt="main_logo">

        @endif
      </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        @if (Auth::user()->userroles->role_name == 'Admin' || Auth::user()->userroles->role_name == 'SuperAdmin' || Auth::user()->userroles->role_name == 'Manager' )
        <li class="nav-item">
          <a class="nav-link text-white   {{request()->is('/') ? 'active bg-gradient-primary' : ''}}" href="{{url('/')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        @endif
        @if (Auth::user()->userroles->role_name == 'Admin' || Auth::user()->userroles->role_name == 'SuperAdmin' || Auth::user()->userroles->role_name == 'Manager' )
        <li class="nav-item">
          <a class="nav-link text-white {{request()->is('fields') ? 'active bg-gradient-primary' : ''}}" href="{{url('/fields')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">table_view</i>
            </div>
            <span class="nav-link-text ms-1">Fields</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white {{request()->is('product-category') ? 'active bg-gradient-primary' : ''}}" href="{{url('/product-category')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">table_view</i>
            </div>
            <span class="nav-link-text ms-1">Categories</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white {{request()->is('uom') ? 'active bg-gradient-primary' : ''}}" href="{{url('/uom')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">table_view</i>
            </div>
            <span class="nav-link-text ms-1">UOM</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white {{request()->is('products') ? 'active bg-gradient-primary' : ''}} " href="/products">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">receipt_long</i>
            </div>
            <span class="nav-link-text ms-1">Items</span>
          </a>
        </li>
        @endif
        <li class="nav-item">
          <a class="nav-link text-white {{request()->is('sales') || request()->segment(1) === 'sales' ? 'active bg-gradient-primary' : ''}} " href="/sales">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">sell</i>
            </div>
            <span class="nav-link-text ms-1">Sales</span>
          </a>
        </li>

        @if (Auth::user()->userroles->role_name == 'Admin' || Auth::user()->userroles->role_name == 'SuperAdmin' || Auth::user()->userroles->role_name == 'Manager' )
        
        <li class="nav-item">
          <a class="nav-link text-white {{request()->is('purchase') || request()->segment(1) === 'purchase' ? 'active bg-gradient-primary' : ''}}" href="/purchase">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">inventory_2</i>
            </div>
            <span class="nav-link-text ms-1">Purchase</span>
          </a>
        </li>


        <li class="nav-item">
          <a class="nav-link text-white {{request()->is('parties') || request()->segment(1) === 'parties' ? 'active bg-gradient-primary' : ''}}" href="{{url('parties')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">people</i>
            </div>
            <span class="nav-link-text ms-1">Parties</span>
          </a>
        </li>


        @if (!ConfigHelper::getStoreConfig()["use_accounting_module"])
        <li class="nav-item">
          <a class="nav-link text-white {{request()->is('ledgers') || request()->is('customer-ledger') || request()->is('vendor-ledger')  || request()->segment(1) === 'ledgers'  ? 'active bg-gradient-primary' : ''}}" href="{{url('ledgers')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">account_balance</i>
            </div>
            <span class="nav-link-text ms-1">Ledgers</span>
          </a>
        </li>
        @else
        <li class="nav-item">
          <a class="nav-link text-white {{request()->is('account') || request()->is('customer-ledger') || request()->is('vendor-ledger')  || request()->segment(1) === 'account'  ? 'active bg-gradient-primary' : ''}}" href="{{url('account')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">account_balance</i>
            </div>
            <span class="nav-link-text ms-1">Accounting</span>
          </a>
        </li>
        @endif

        <li class="nav-item">
          <a class="nav-link text-white {{request()->is('reports') || request()->segment(1) === 'reports' ? 'active bg-gradient-primary' : ''}}" href="{{url('reports')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">bar_chart</i>
            </div>
            <span class="nav-link-text ms-1">Reports</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white {{request()->is('users') || request()->segment(1) === 'users' ? 'active bg-gradient-primary' : ''}}" href="/users">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">people</i>
            </div>
            <span class="nav-link-text ms-1">Users</span>
          </a>
        </li>
      
       
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">System Settings</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white {{request()->is('system') || request()->segment(1) === 'system' ? 'active bg-gradient-primary' : ''}}" href="{{url('/system/configurations')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">settings</i>
            </div>
            <span class="nav-link-text ms-1">Setting</span>
          </a>
        </li>
       
        @endif
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Account pages</h6>
        </li>
        {{-- <li class="nav-item">
          <a class="nav-link text-white " href="../pages/profile.html">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">person</i>
            </div>
            <span class="nav-link-text ms-1">Profile</span>
          </a>
        </li> --}}
       
        <li class="nav-item">
          <a class="nav-link text-white " href="{{route('auth.logout')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">logout</i>
            </div>
            <span class="nav-link-text ms-1">Sign Out</span>
          </a>
        </li>
      </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0 ">
      <div class="mx-3">
        <a class="btn bg-gradient-primary mt-4 w-100" target="_blank" href="https://wa.me/03200681969?text=Hello,%20%0AName:%20{{Auth::check() ? Auth::user()->name : ''}}%0AStore:%20{{Auth::check() && isset(Auth::user()->store)? Auth::user()->store->store_name : ''}}%0AUser%20Role:%20{{Auth::check() && isset(Auth::user()->userroles->role_name) ? Auth::user()->userroles->role_name : ''}}%0AI%20need%20help%20regarding...."  type="button">Need Help?</a>
      </div>
    </div>
  </aside>