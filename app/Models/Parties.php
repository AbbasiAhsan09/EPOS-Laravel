<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parties extends Model
{
    use HasFactory , SoftDeletes;
    /**
     * Get the user that owns the Parties
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groups(): BelongsTo
    {
        return $this->belongsTo(PartyGroups::class, 'group_id', 'id');
    }


    /**
     * Get all of the sales for the Parties
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sales::class, 'customer_id', 'id');
    }

        /**
     * Get all of the sales for the Parties
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(PurchaseInvoice::class, 'party_id', 'id');
    }
}
