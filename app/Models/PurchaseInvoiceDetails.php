<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoiceDetails extends Model
{
    use HasFactory, SoftDeletes, UniversalScopeTrait;

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

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
