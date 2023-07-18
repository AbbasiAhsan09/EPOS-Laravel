<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseQuotation extends Model
{

    use HasFactory, SoftDeletes, UniversalScopeTrait;

    /**
     * Get all of the details for the PurchaseQuotation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(PurchaseQuotationDetails::class, 'quotation_id', 'id');
    }

    /**
     * Get the created_by that owns the PurchaseQuotation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
public function created_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the party that owns the PurchaseQuotation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function party(): BelongsTo
    {
        return $this->belongsTo(Parties::class, 'party_id', 'id');
    }
}
