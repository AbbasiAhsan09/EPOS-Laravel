<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$order->tran_no  ?? "Invoice"}}</title>
    <script src="{{asset("js/jquery.min.js")}}"></script>
    <style>

            @font-face {
                font-family: 'Invoice Fonts';
                src: url('{{asset("fonts/texta/TextaRegular.ttf")}}');
                font-weight: normal;
                font-style: normal;
                font-display: swap;
            }




        *{
            margin: 0;
            box-sizing: border-box;
            font-family: 'Invoice Fonts';
        }
        body{
            width: 100%;
        }

        @media print {
        body {
            zoom: 80%;
        }
        }

        .invoice-wrapper{
            background: white;
            height: 100%;
            padding: 10px;
            border: dotted 1px black;
            margin: 10px
        }
        header{
            display: flex;
            align-items: center;
            justify-content: space-between;
      

        }
        .invoice-meta > p{
            /* min-width: 200px; */
            width: 250px;
            max-width: 400px;
            margin:  5px 0;
            padding: 5px;
          
            border: solid 1px black
        }
        footer{
            text-align: center;
            margin: 10px 0
        }
        .copyrights{
            font-weight: 600;
            margin: 10px 00
        }
        
    </style>
</head>
<body>

    @php
        $config = ConfigHelper::getStoreConfig();
    @endphp

   <div class="invoice-wrapper">
    <header>
        <div class="branding">
            @if ($config && $config['logo'] && $config["invoice_logo"])
                <img src="{{asset("images/logo/".$config['logo']."")}}" alt="Not Available" width="150px">
            @endif
           
        </div>
        <div class="invoice-heading">
            @if ($config && $config['invoice_name'] && $config["app_title"])
            <h4 >{{$config["app_title"]}}</h4>
        @endif
             <h2 style="text-align: center; margin-top: 10px">Invoice</h2>
        </div>
        <div class="invoice-meta">
            <p><strong>Invoice No. {{$order->tran_no ?? ""}}</strong></p>
            <p><strong>Date : {{$order->bill_date !== null ? date('d/m/Y',strtotime($order->bill_date )) :date('d/m/Y', strtotime($order->created_at))}}</strong></p>
        </div>
    </header>
    @yield('invoice_content')

    <footer>
        @if ($config && $config["invoice_message"])
        <p>{{$config["invoice_message"]}}</p>
        <br>
        @endif
        <hr>
        <p class="copyrights">
            
            This software is developed by TradeWisePOS | +923200681969 
        </p>
    </footer>
   </div>
    <script>
        $(document).ready(function() {
      // print the window when a button is clicked
        // enable automatic printing in Chrome
        var printSettings = {
          'mediaSize': { 'name': 'na_legal', 'height_microns': 355.6, 'width_microns': 215.9, 'custom_display_name': 'Legal' },
          'shouldPrintBackgrounds': true
        };
        if (navigator.userAgent.indexOf('Chrome') !== -1) {
          printSettings['autoprint'] = true;
        }
        // print the window
        window.print();
        setTimeout(() => {
            window.close();
        }, 1000);
    });
    </script>

    <style>

        table.invoice-table {
            width: 100%;
        }
        table.invoice-table , table.invoice-table  th, table.invoice-table td{
            border: 1px solid black;
            border-collapse: collapse; 
            padding: 5px;
            text-align: left !important
        }
        table.invoice-table th {
            background: rgb(239, 239, 239)
        }

        .invoice-table td{
           padding: 10px 5px !important
        }

    </style>
</body>
</html>