<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturn extends Model
{   
    //table
    protected $table = 'sale_returns';
    protected  $fillable = [
        'store_id',
        'doc_no',
        'user_id',
        'sale_id',
        'party_id',
        'return_date',
        'reason',
        'total',
        'other_charges',
        'invoice_no',
        'refunded_amount',
        "discount_type",
        "discount",
        "net_total"
    ];
    

    /**
     * Get all of the order_details for the SaleReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function order_details(): HasMany
    {
        return $this->hasMany(SaleReturnDetail::class, 'sale_id', 'id');
    }



    /**
     * Get the user that owns the SaleReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the party that owns the SaleReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function party(): BelongsTo
    {
        return $this->belongsTo(Parties::class, 'party_id', 'id');
    }
    use HasFactory , SoftDeletes, UniversalScopeTrait;
}
