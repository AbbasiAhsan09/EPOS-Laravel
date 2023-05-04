<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoice extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['updated_at' , 'created_at' ,'recieved' ,'net_amount'];
    /**
     * Get the order that owns the PurchaseInvoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'id');
    }

    /**
     * Get the created_by_user that owns the PurchaseInvoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the party that owns the PurchaseInvoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function party(): BelongsTo
    {
        return $this->belongsTo(Parties::class, 'party_id', 'id');
    }

    public function details() : HasMany
    {
        return $this->hasMany(PurchaseInvoiceDetails::class, 'inv_id' , 'id');
    }
}
