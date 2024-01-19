@php
    use App\Models\Configuration;
    $currenConfig = Configuration::filterByStore()->first();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
  
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{asset('img/apple-icon.png')}}">
  <link rel="icon" type="image/png" href="{{asset('images/icon.png')}}">
  <title>
    {{env('APP_TITLE')}}
  </title>
  {{-- Custom CSS --}}
  <link rel="stylesheet" href="{{asset('css/custom.css')}}">

  {{-- Sweet Alert CDN --}}
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
  <!-- Nucleo Icons -->
  <link href="{{asset('css/nucleo-icons.css')}}" rel="stylesheet" />
  <link href="{{asset('css/nucleo-svg.css')}}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{asset('fontawesome-free-6.4.0-web/css/all.css')}}">
  {{-- <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script> --}}
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="{{asset('css/material-dashboard.css?v=3.0.4')}}" rel="stylesheet" />
  <script src="{{asset('js/plugins/jquery.min.js')}}"></script>
  @livewireStyles
</head>

<body class="g-sidenav-show  bg-gray-200">
  <input type="hidden" id="checkInventory" value="{{$currenConfig->inventory_tracking ?? 0}}">
  <input type="hidden" id="storeId" value="{{Auth::user()->store->id ?? 0}}">
  @include('sweetalert::alert')
  @if (Auth::check())
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


        <li class="nav-item">
          <a class="nav-link text-white {{request()->is('ledgers') || request()->is('customer-ledger') || request()->is('vendor-ledger')  || request()->segment(1) === 'ledgers'  ? 'active bg-gradient-primary' : ''}}" href="{{url('ledgers')}}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">account_balance</i>
            </div>
            <span class="nav-link-text ms-1">Ledgers</span>
          </a>
        </li>

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
  @endif
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    @include('includes.top_nav')

        <main class="py-2">
            @yield('content')
        </main>
    </div>
    @livewireScripts
   </div>
  </main>
  <div class="fixed-plugin">
    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
      <i class="material-icons py-2">settings</i>
    </a>
    <div class="card shadow-lg">
      <div class="card-header pb-0 pt-3">
        <div class="float-start">
          <h5 class="mt-3 mb-0">{{$currenConfig->app_title ?? ""}}</h5>
          {{-- <p>See our dashboard options.</p> --}}
        </div>
        <div class="float-end mt-4">
          <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
            <i class="material-icons">clear</i>
          </button>
        </div>
        <!-- End Toggle Button -->
      </div>
      <hr class="horizontal dark my-1">
      <div class="card-body pt-sm-3 pt-0">
        <!-- Sidebar Backgrounds -->
        <div>
          <h6 class="mb-0">Sidebar Colors</h6>
        </div>
        <a href="javascript:void(0)" class="switch-trigger background-color">
          <div class="badge-colors my-2 text-start">
            <span class="badge filter bg-gradient-primary active" data-color="primary" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-dark" data-color="dark" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
          </div>
        </a>
        <!-- Sidenav Type -->
        <div class="mt-3">
          <h6 class="mb-0">Sidenav Type</h6>
          <p class="text-sm">Choose between 2 different sidenav types.</p>
        </div>
        <div class="d-flex">
          <button class="btn bg-gradient-dark px-3 mb-2 active" data-class="bg-gradient-dark" onclick="sidebarType(this)">Dark</button>
          <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent" onclick="sidebarType(this)">Transparent</button>
          <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-white" onclick="sidebarType(this)">White</button>
        </div>
        <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
        <!-- Navbar Fixed -->
        <div class="mt-3 d-flex">
          <a href="{{url("/profile")}}"><h6 class="mb-0">Change Password</h6></a>
          {{-- <div class="form-check form-switch ps-0 ms-auto my-auto">
            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
          </div> --}}
        </div>
        <hr class="horizontal dark my-3">
        {{-- <div class="mt-2 d-flex">
          <h6 class="mb-0">Light / Dark</h6>
          <div class="form-check form-switch ps-0 ms-auto my-auto">
            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">
          </div>
        </div> --}}
      </div>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="{{asset('js/core/popper.min.js')}}"></script>
  <script src="{{asset('js/core/bootstrap.min.js')}}"></script>
  <script src="{{asset('js/plugins/perfect-scrollbar.min.js')}}"></script>
  <script src="{{asset('js/plugins/smooth-scrollbar.min.js')}}"></script>
  <script src="{{asset('js/plugins/chartjs.min.js')}}"></script>
  <script>
    var ctx = document.getElementById("chart-bars").getContext("2d");

    $.ajax({
      url : 'charts/weekly-sales',
      type : 'GET',
      success: function(res){
        new Chart(ctx, {
      type: "bar",
      data: {
        labels: res.label,
        datasets: [{
          label: "Sales",
          tension: 0.4,
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false,
          backgroundColor: "rgba(255, 255, 255, .8)",
          data: res.data,
          maxBarThickness: 6
        }, ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              suggestedMin: 0,
              suggestedMax: 500,
              beginAtZero: true,
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
              color: "#fff"
            },
          },
          x: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });
      }
    })
    


    var ctx2 = document.getElementById("chart-line").getContext("2d");

    $.ajax({
      url : 'charts/monthly-sales',
      type : 'GET',
      success : function(res){
        new Chart(ctx2, {
      type: "line",
      data: {
        labels: res.label,
        datasets: [{
          label: "Sales",
          tension: 0,
          borderWidth: 0,
          pointRadius: 5,
          pointBackgroundColor: "rgba(255, 255, 255, .8)",
          pointBorderColor: "transparent",
          borderColor: "rgba(255, 255, 255, .8)",
          borderColor: "rgba(255, 255, 255, .8)",
          borderWidth: 4,
          backgroundColor: "transparent",
          fill: true,
          data: res.data,
          maxBarThickness: 6

        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });
      }
    });
    

    var ctx3 = document.getElementById("chart-line-tasks").getContext("2d");

    $.ajax({
      url : '/charts/monthly-purchases',
      type : 'GET',
      success: function(res){
        new Chart(ctx3, {
      type: "line",
      data: {
        labels: res.label,
        datasets: [{
          label: "Purchases",
          tension: 0,
          borderWidth: 0,
          pointRadius: 5,
          pointBackgroundColor: "rgba(255, 255, 255, .8)",
          pointBorderColor: "transparent",
          borderColor: "rgba(255, 255, 255, .8)",
          borderWidth: 4,
          backgroundColor: "transparent",
          fill: true,
          data: res.data,
          maxBarThickness: 6

        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              display: true,
              padding: 10,
              color: '#f8f9fa',
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });
      }
    });
  
  </script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{asset('js/material-dashboard.min.js?v=3.0.4')}}"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

 @yield('scripts')
 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

 <script>
   $(document).on('keydown', function(event) {
        if (event.key === '/') {
          setTimeout(() => {
            $('#searchItemValue').focus();
          }, 500);
        }
      });
 </script>

</body>

</html>

<style>
    .row-customized{
        align-items: center;
        justify-items: center;
        align-content: center;
        justify-content: center
    }


</style>

