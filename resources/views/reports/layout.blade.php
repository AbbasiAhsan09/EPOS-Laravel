<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        {{$report_title ?? "Report"}} 
        {{ (isset($from) && isset($to) && $from && $to) ? ' - From: '.date('d/m/Y', strtotime($from)).' To: '.date('d/m/Y', strtotime($to)) : '' }}
    </title>
    <style>
        @font-face {
            font-family: 'texta';
            src: url('{{asset("fonts/texta/TextaRegular.ttf")}}') format('woff2'),
                url('{{asset("fonts/texta/TextaRegular.ttf")}}') format('woff');
            font-weight: normal;
            font-style: normal;
        }

        @page { margin: 10px; }
        body { margin: 0px; }
        
        *{
            box-sizing: border-box;
            font-family: 'texta', sans-serif;
        }


        .main-header{
            background: #9ec200;
            padding: 10px;
            max-height: 200px;
            display: flex;
            align-items: center;
            justify-content: space-between
            
        }

        .main-header *{
            border: none;
            text-transform: uppercase
        }
       
        .main-header img, .vendor > img, .brand > img{
            filter: grayscale(100%) brightness(0%);
            max-height: 60px;
        }
        .brand{
            display: flex;
            align-items: center;
            color: white;
            width: fit-content
        }
        .brand *{
            margin: 0 !important;
           
        }
        .vendor{
            width:fit-content ;
        }
        .info-header{
            padding: 5px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #cecdcd;
            text-transform: uppercase;
            font-size: 12px
        }

        .info-header * {
            margin: 0;
            padding: 0;
            border: none
        }
        footer{
            background: #9ec200;
            color: black;
            text-transform: uppercase;
            text-align: center;
            padding: 5px;
            font-weight: 700;
            font-size: 10px
        }
        tr * {
            font-size: 10px !important
        }


        @media print{
            .main-header{
                display: flex;
            align-items: center;
            justify-content: space-between
            
            }

            .main-header img, .vendor > img, .brand > img{
            filter: grayscale(100%) brightness(0%);
            max-height: 60px;
        }

        
            .info-header{
                display: flex;
            align-items: center;
            justify-content: space-between;
            }
        }
    </style>
</head>

<body>
    <header class="main-header">

        <table>
            <thead>
                <th style="text-align: left; width: 200px">
                    <div class="brand" >
            @if (ConfigHelper::getStoreConfig() && ConfigHelper::getStoreConfig()['logo'] && ConfigHelper::getStoreConfig()['invoice_logo'])
            <img src="{{ 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(public_path('images/logo/' . ConfigHelper::getStoreConfig()['logo']))) }}" 
            alt="Not Available" 
            style="margin-top: 20px;" 
            width="80px" 
            class="inv_logo">
            @elseif(ConfigHelper::getStoreConfig() && ConfigHelper::getStoreConfig()['invoice_name'])
                <h2 style="text-transform: uppercase">{{ConfigHelper::getStoreConfig() ? ConfigHelper::getStoreConfig()['app_title'] : 'Demo'}}</h2>
            @endif
            @if (ConfigHelper::getStoreConfig() && ConfigHelper::getStoreConfig()['invoice_logo'] && ConfigHelper::getStoreConfig()['invoice_name'])
           
            <strong style="text-transform: uppercase">{{ConfigHelper::getStoreConfig() ? ConfigHelper::getStoreConfig()['app_title'] : 'Demo'}}</strong>
            @endif
        </div>
                </th>
                <th style="text-align: center; width: 200px; text-decoration : underline">{{$report_title ?? "Report"}}</th>
                <th style="text-align: right; width: 200px">
                     <div class="vendor">
                        <img src="{{asset("images/logo.png")}}" alt="Not Available" width="80px">
                    </div>
                </th>
            </thead>

        </table>


        
       
    </header>
    <div class="info-header">
        <table>
            <thead>
                <th style="text-align: left; width: 200px">Printed On: {{date("d/m/Y")}}</th>
                <th style="text-align: center; width: 200px"></th>
                <th style="text-align: right; width: 200px">
                    {{ (isset($from) && isset($to) && $from && $to) ? 'From: '.date('d/m/Y', strtotime($from)).' To: '.date('d/m/Y', strtotime($to)) : '' }}
                </th>
            </thead>

        </table>
        
    </div>
    <div class="report-content">
        @yield('report_content')
    </div>
   
    <footer>
        Powered by TradeWisePOS - PH : +92-320-0681969
    </footer>

    <style>
        table{
            width: 100% ;
        }
        table, th, td {
      border: 1px solid gray;
      border-collapse: collapse;
    }
        .dates{
            float: right
        }

        .report-content table th{
            font-size: 12px !important;
            background : black;
            color: white
        }
    </style>
</body>
</html>