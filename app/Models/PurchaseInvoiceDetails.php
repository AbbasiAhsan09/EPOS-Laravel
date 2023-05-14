<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoiceDetails extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the items that owns the PurchaseInvoiceDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function items(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'item_id', 'id');
    }
    public function invoice() : BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class , 'inv_id' , 'id');
    }
}
