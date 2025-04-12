<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesDetails extends Model
{
    protected $table = 'sales_details';
    /**
     * Get the user that owns the SalesDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item_details(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'item_id', 'id');
    }

    /**
     * Get the order that owns the SalesDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'sale_id', 'id');
    }


    /**
     * Get the unit that owns the SalesDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
    use HasFactory;
}
