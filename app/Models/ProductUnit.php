<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductUnit extends Model
{
    protected $fillable = [
        'product_id',
        'unit_id',
        'conversion_rate',
        'unit_rate',
        'unit_cost',
        'default',
        'unit_barcode',
        'description',
        'convert_to_unit_id',
        'symbol',
        'is_active',
        'conversion_divider',
        'created_by',
        'store_id',
    ];
    protected $casts = [
        'default' => 'boolean',
        'is_active' => 'boolean',
    ];
    protected $table = 'product_units';

    /**
     * Get the unit that owns the ProductUnit
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
    use HasFactory, SoftDeletes, UniversalScopeTrait;
}
