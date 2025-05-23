<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoice extends Model
{
    use HasFactory, SoftDeletes, UniversalScopeTrait;
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

    /**
     * Get all of the transactions for the PurchaseInvoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PurchaseTransactions::class, 'p_inv_id', 'id');
    }


    public function dynamicFeildsData(): HasMany
    {
        return $this->hasMany(AppFormFieldsData::class, 'related_to', 'id')->where('form_id', 4);
    }
}
