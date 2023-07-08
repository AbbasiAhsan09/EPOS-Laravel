<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderTransactions extends Model
{
    protected $table = 'order_transactions';
    protected $fillable = ['order_id','customer_id','amount','status','description','created_at','updated_at','user_id'];

    /**
     * Get the sale that owns the OrderTransactions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'order_id', 'id');
    }
    use HasFactory, SoftDeletes;
}
