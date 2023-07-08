<?php

namespace App\Http\Trait;

use App\Models\OrderTransactions;
use App\Models\PurchaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Mockery\Matcher\Any;

/**
 * 
 */
trait TransactionsTrait
{
    public function createOrderTransactionHistory(
        int $order_id,
        int $customer_id,
        int $amount,
        $date,
        string $status = 'received',
        string $description = null
    ) {
        $transaction =   OrderTransactions::create([
            'order_id' => $order_id,
            'customer_id' => $customer_id,
            'amount' => $amount,
            'status' => $status,
            'user_id' => Auth::user()->id,
            'description' => $description,
            'created_at' => (strtotime($date)),
            'updated_at' => (strtotime($date))
        ]);

        if ($transaction) {
            return $transaction;
        }

        return false;
    }



    public function updateOrderTransaction(int $order_id, int $customer_id, int $old_amount, int $new_amount, string $description)
    {
        $amount = ($new_amount - $old_amount);
        $status = ($amount < 0 ? 'paid' : 'recieved');
        OrderTransactions::create([
            'order_id' => $order_id,
            'customer_id' => $customer_id,
            'amount' => $amount,
            'status' => $status,
            'user_id' => Auth::user()->id,
            'description' => $description,
            'created_at' => time(),
            'updated_at' => time()
        ]);
    }


    // Purchase

    public function createPurchaseTransactionHistory(
        int $p_inv_id,
        int $vendor_id,
        int $amount,
        $date,
        string $status = 'received',
        string $description = null
    ) {
        $transaction =   PurchaseTransactions::create([
            'p_inv_id' => $p_inv_id,
            'vendor_id' => $vendor_id,
            'amount' => $amount,
            'status' => $status,
            'user_id' => Auth::user()->id,
            'description' => $description,
            'created_at' => (strtotime($date)),
            'updated_at' => (strtotime($date))
        ]);

        if ($transaction) {
            return $transaction;
        }

        return false;
    }


    public function updatePurchaseTransaction(int $p_inv_id, int $party_id, int $old_amount, int $new_amount, string $description)
    {
        $amount = ($new_amount - $old_amount);
        $status = ($amount < 0 ? 'recieved' : 'paid');
        PurchaseTransactions::create([
            'p_inv_id' => $p_inv_id,
            'vendor_id' => $party_id,
            'amount' => $amount,
            'status' => $status,
            'user_id' => Auth::user()->id,
            'description' => $description,
            'created_at' => time(),
            'updated_at' => time()
        ]);
    }
}
