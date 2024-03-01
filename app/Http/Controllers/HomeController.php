<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\Sales;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $saleBalance = Sales::select('net_total','recieved')->byUser()->where('customer_id',0)->get();
        $saleBalanceParties = Sales::select('net_total','recieved')->where('customer_id','!=',0)->byUser()->get();
        $purchaseBalance = PurchaseInvoice::select('net_amount','recieved',)->byUser()->get();

        $sales = Sales::whereMonth('created_at', $currentMonth)->byUser()->whereYear('created_at', $currentYear)
            ->orderBy('id', 'DESC')->get();
        $purchases = PurchaseInvoice::whereMonth('created_at', $currentMonth)->byUser()->whereYear('created_at', $currentYear)->get();

        $totalSales = Sales::byUser()->sum("net_total");
        $totalPurchase = PurchaseInvoice::byUser()->sum("net_amount");
        
        return view('home', compact('sales', 'purchases','saleBalance', 'purchaseBalance','saleBalanceParties', 'totalPurchase', 'totalSales'));
    }

    public function reports()
    {
        return view('reports.reports');
    }


    // Ajax Request 
    public function weeklySales()
    {
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();
        $sales = Sales::selectRaw('DATE_FORMAT(created_at, "%a") as day, SUM(net_total) as total')->byUser()
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
            ->groupBy('day')
            ->get();
        $label = [];
        $data = [];

        foreach ($sales as $key => $record) {
            array_push($label, $record->day);
            array_push($data, round($record->total));
        }

        $records = [
            'label' => $label,
            'data' => $data
        ];
        return  response()->json($records);
    }

    public function monthlySales()
    {
       
        $currentYear = Carbon::now()->year;

        $records = Sales::selectRaw('MONTH(created_at) as date, SUM(net_total) as total')->byUser()
            ->whereYear('created_at', $currentYear)
            ->groupBy('date')
            ->get();

        $label = [];
        $data = [];

        foreach ($records as  $record) {
         
            array_push($label, Carbon::create()->day(1)->month($record->date)->format('F'));
            array_push($data, round($record->total));
        }

        $records = [
            'label' => $label,
            'data' => $data
        ];


        return $records;
    }

    public function purchaseMonthlySales()
    {
        $currentYear = Carbon::now()->year;
        
        $records = PurchaseInvoice::selectRaw('MONTH(created_at) as date, SUM(net_amount) as total')->byUser()
            ->whereYear('created_at', $currentYear)
            ->groupBy('date')
            ->get();

        $label = [];
        $data = [];

        foreach ($records as  $record) {
            array_push($label, Carbon::create()->day(1)->month($record->date)->format('F'));
            array_push($data, round($record->total));
        }

        $records = [
            'label' => $label,
            'data' => $data
        ];


        return $records;
    }
}
