<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseTransactions extends Model
{
    protected $table = 'purchase_invoice_transactions';
    protected $fillable = ['p_inv_id','vendor_id','amount','status','description','created_at','updated_at','user_id'];

    use HasFactory, SoftDeletes;
}
