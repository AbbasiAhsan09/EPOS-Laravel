<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequestDetail extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the items that owns the PurchaseRequestDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function items(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'item_id', 'id');
    }
}
