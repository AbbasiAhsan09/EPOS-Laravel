<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturnDetail extends Model
{
    protected $table = 'sale_return_details';
    protected $fillable = [
        'sale_id',
        'item_id',
        'original_qty',
        'is_base_unit',
        'returned_qty',
        'original_rate',
        'returned_rate',
        'original_tax',
        'returned_tax',
        'original_disc',
        'returned_disc',
        'original_total',
        'returned_total',
        'bags',
        'bag_size',
        'unit_id',
        'unit_conversion_rate',
        'status',
        'base_unit_value'
    ];

    public function item_details(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'item_id', 'id');
    }


    /**
     * Get the return that owns the SaleReturnDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function return(): BelongsTo
    {
        return $this->belongsTo(SaleReturn::class, 'sale_id', 'id');
    }
    
    use HasFactory , SoftDeletes, UniversalScopeTrait;
}
