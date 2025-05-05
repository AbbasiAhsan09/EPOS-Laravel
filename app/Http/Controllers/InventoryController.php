<?php

namespace App\Http\Controllers;

use App\Http\Trait\InventoryTrait;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Inventory;
use App\Models\ProductUnit;
use App\Models\PurchaseInvoice;
use App\Models\SaleReturn;
use App\Models\Sales;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    use InventoryTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(Inventory $inventory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventory $inventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventory $inventory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        //
    }

    public function checkInventory($item_id, $is_base_unit)
    {
        try {
          return  response()->json($this->checkAvaialableInventory($item_id , $is_base_unit));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function generate_old_cogs_data()  {
        $this->generate_cogs();
        $this->generate_cogs_return();
    }

    static function generate_cogs($params = null){
       try {
         // initialy playing with 10 invoices
         $sales = Sales::with("order_details")->where('store_id', Auth::user()->store_id)
         ->orderBy('bill_date',"asc");
         $deleteArray = AccountTransaction::where("reference_type",'sale_cogs');
         if(isset($params) && isset($params['sale_id']) && !empty($params['sale_id'])){
            $deleteArray = $deleteArray->where('reference_id',$params["sale_id"]);
            $sales = $sales->where('id',$params["sale_id"]);
         }

         $sales = $sales->get();

         $deleteArray = $deleteArray->delete();
         
         $cogs_account = Account::where('account_number',5000)->filterByStore()->first();
         $inventory_account = Account::where('account_number',1030)->filterByStore()->first();
    


    
         $start_date =Carbon::createFromDate(1999, 1, 1)->startOfDay();
         foreach ($sales as $key => $sale) {
            
             $amount = 0;
             $end_date = Carbon::parse($sale->bill_date)->addDay()->endOfDay();
 
             if ($sale->order_details && count($sale->order_details )) {
                 foreach ($sale->order_details as $key => $detail) {
                 $base_quantity = $detail->qty;
                 $conversion_rate = $detail->unit_conversion_rate ?? 1;
                 $base_quantity = $base_quantity * $conversion_rate;
                //  if(isset($detail->item_details->product_units) && $detail->item_details->product_units && count($detail->item_details->product_units) > 0){

                //  }
                 
                 $request =  new Request();
                 $request->merge([
                     'product' => $detail->item_id,
                     'start_date' => $start_date,
                     'end_date' => $end_date
                 ]);
                 $inventory =  InventoryReportController::inventory_report($request)->first();
                 $amount += ($inventory->avg_rate ?? $inventory->tp) * $base_quantity;
                 }
             }
 
             if($amount > 0){
                
                 AccountController::record_journal_entry([
                     'account_id' => $inventory_account->id,
                     'credit' => $amount,
                     'debit' => 0,
                     'reference_id' => $sale->id,
                     'reference_type' => 'sale_cogs',
                     'note' => 'Sold Items cost for order '. ($sale->tran_no ?? "")." @" .number_format($amount,2),
                     'source_account' => $cogs_account->id,
                     'transaction_date' => $sale->bill_date ? $sale->bill_date : date('Y-m-d',strtotime($sale->created_at))
                 ]);
             }
 
         }

        
       } catch (\Throwable $th) {
        throw $th;
       }
    }

    static function generate_cogs_return($params = null){
        try {
          // initialy playing with 10 invoices
          $sales_returns = SaleReturn::with("order_details")->where('store_id', Auth::user()->store_id)
          ->orderBy('return_date',"asc");
          $deleteArray = AccountTransaction::where("reference_type",'sale_return_cogs');
          if(isset($params) && isset($params['sale_return_id']) && !empty($params['sale_return_id'])){
             $deleteArray = $deleteArray->where('reference_id',$params["sale_return_id"]);
             $sales_returns = $sales_returns->where('id',$params["sale_return_id"]);
          }
 
          $sales_returns = $sales_returns->get();
          $deleteArray = $deleteArray->delete();
          
          $cogs_account = Account::where('account_number',5000)->filterByStore()->first();
          $inventory_account = Account::where('account_number',1030)->filterByStore()->first();
     
 
 
        //   dd($sales_returns);/
     
          $start_date =Carbon::createFromDate(1999, 1, 1)->startOfDay();
          foreach ($sales_returns as $key => $sale_return) {
              $amount = 0;
              $end_date = Carbon::parse($sale_return->return_date)->addDay()->endOfDay();
  
              if ($sale_return->order_details && count($sale_return->order_details )) {
                  foreach ($sale_return->order_details as $key => $detail) {
                  $base_quantity = $detail->returned_qty;
                  $conversion_rate = $detail->unit_conversion_rate ?? 1;
                  $base_quantity = $base_quantity * $conversion_rate;
                 //  if(isset($detail->item_details->product_units) && $detail->item_details->product_units && count($detail->item_details->product_units) > 0){
 
                 //  }
                  
                  $request =  new Request();
                  $request->merge([
                      'product' => $detail->item_id,
                      'start_date' => $start_date,
                      'end_date' => $end_date
                  ]);
                  $inventory =  InventoryReportController::inventory_report($request)->first();
                  $amount += ($inventory->avg_rate ?? $inventory->tp) * $base_quantity;
                  }
              }
  
              if($amount > 0){
                 // dd('hi', $amount);
                  AccountController::record_journal_entry([
                      'account_id' => $inventory_account->id,
                      'credit' => 0,
                      'debit' => $amount,
                      'reference_id' => $sale_return->id,
                      'reference_type' => 'sale_return_cogs',
                      'note' => 'Sold Items cost reversal for return '. ($sale_return->doc_no ?? "")." @" .number_format($amount,2),
                      'source_account' => $cogs_account->id,
                      'transaction_date' => $sale_return->return_date ? $sale_return->return_date : date('Y-m-d',strtotime($sale_return->created_at))
                  ]);
              }
  
          }
        } catch (\Throwable $th) {
         throw $th;
        }
     }

    public function check_inventory_by_item(Request $request) {
       try{

        $required_qty = $request->query('qty');
        $unit_id = $request->query('unit_id');
        $item_id = $request->query('product');
        $low_stock = false;
        $avl_stock = 0;
        if($request->query('product')){
            $result = InventoryReportController::inventory_report($request);
        
            $result = $result->first();

            if($unit_id){
                $product_unit = ProductUnit::where("unit_id", $unit_id)->where("product_id", $item_id)->first();
                // dump($product_unit, $unit_id, $item_id);
                if($product_unit){
                    $required_qty = $product_unit->conversion_multiplier * $required_qty;
                    $avl_stock = $result->avl_qty / $product_unit->conversion_multiplier;
                }
            }else{
                $avl_stock = $result->avl_qty;
                $low_stock = $result->avl_qty < $required_qty ? true : false;
            }

            if($result){
                if($required_qty > $result->avl_qty){
                    $low_stock = true;
                }
            }
            $result = json_decode(json_encode($result), true);
            // dump($result);
            $result['low_stock'] = $low_stock;
            $result['avl_stock'] = number_format((float)$avl_stock, 2, '.', '');;

            return response()->json($result);
        }

        return response()->json(null);

       }catch (\Throwable $th) {
            throw $th;
        }
    }
}
