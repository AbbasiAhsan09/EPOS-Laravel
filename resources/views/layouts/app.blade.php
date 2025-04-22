@php
    use App\Models\Configuration;
    use App\Models\KeyboardShortcut;
   
    $currenConfig = Configuration::filterByStore()->first();
    $shortcuts = KeyboardShortcut::all();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
  
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{asset('img/apple-icon.png')}}">
  <link rel="icon" type="image/png" href="{{asset('images/icon.png')}}">
  <title>
    {{Auth::check() ? isset(ConfigHelper::getStoreConfig()["app_title"]) ? ConfigHelper::getStoreConfig()["app_title"] : env('APP_TITLE') : env('APP_TITLE')  }}
  </title>
  {{-- Custom CSS --}}
  <link rel="stylesheet" href="{{asset('css/custom.css')}}">

  {{-- Sweet Alert CDN --}}
  <script src="{{asset("js/sweetalert.min.js")}}"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
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

  <script src="{{asset("js/plugins/moment.min.js")}}"></script>

  <script src="{{asset('js/plugins/jquery.min.js')}}"></script>
  @livewireStyles
</head>

<body class="g-sidenav-show  bg-gray-200">
  <input type="hidden" id="checkInventory" value="{{$currenConfig->inventory_tracking ?? 0}}">
  <input type="hidden" id="lowInventoryCheck" value="{{$currenConfig->allow_low_inventory ?? 0}}">
  <input type="hidden" id="show_bag_sizing" value="{{ $currenConfig->show_bag_sizing ?? 0}}">
  <input type="hidden" id="cash_printer_thermal" value="{{ $currenConfig->cash_printer_thermal ?? 0}}">
  <input type="hidden" id="credit_printer" value="{{ $currenConfig->credit_printer ?? 0}}">
  <input type="hidden" id="receipt_printer" value="{{ $currenConfig->receipt_printer ?? 0}}">
  <input type="hidden" id="storeId" value="{{Auth::user()->store->id ?? 0}}">
  @include('sweetalert::alert')
  @if (Auth::check())
  @if (ConfigHelper::getStoreConfig()["ui"] === 'classic')
  @include("includes.vertical-nav")
  @else
  @include("includes.horizontal-nav")
  @endif
  @endif
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    {{-- @include('includes.top_nav') --}}

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

  @if ($shortcuts && $shortcuts->count())
      <script>
         const shortcuts = @json($shortcuts);
     
          document.addEventListener('keydown', function (event) {
              // Ignore keypresses in input fields or editable elements
              if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA' || event.target.isContentEditable) {
                  return;
              }

              shortcuts.forEach(shortcut => {
                const [modifier, key] = shortcut.key.toLowerCase().split(' + ');
                  if (
                      (modifier === 'ctrl' && event.ctrlKey) ||
                      (modifier === 'alt' && event.altKey) ||
                      (modifier === 'shift' && event.shiftKey) ||
                      (modifier === 'cmd' && event.metaKey)
                  ) {
                      if (event.key.toLowerCase() === key) {
                          event.preventDefault(); // Prevent default action
                          window.location.href = shortcut.uri; // Navigate to the action
                      }
                  }
              });
          });
      </script>
  @endif

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
  <script async defer src="{{asset("externals/github/buttons.js")}}"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{asset('js/material-dashboard.min.js?v=3.0.4')}}"></script>
  <link href="{{asset("externals/select2/select2.min.css")}}" rel="stylesheet" />

 @yield('scripts')
 <script src="{{asset("externals/select2/select2.min.js")}}"></script>
 <script src="{{asset("js/multiple-form-submit-prevent.js")}}"></script>

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

