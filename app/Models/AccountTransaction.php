<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountTransaction extends Model
{
    protected $table = 'account_transactions';
    protected $fillable = [
        'store_id',
        'account_id',
        'reference_type',
        'reference_id',
        'credit',
        'debit',
        'note',
        'transaction_date',
        'recorded_by',
        'source_account'
    ];


    use HasFactory, SoftDeletes, UniversalScopeTrait;


   /**
    * Get the account that owns the AccountTransaction
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
   public function account(): BelongsTo
   {
       return $this->belongsTo(Account::class, 'account_id', 'id');
   }


    /**
    * Get the account that owns the AccountTransaction
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function source_account_detail(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'source_account', 'id');
    }


    /**
     * Get the sale that owns the AccountTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'reference_id', 'id');
    }

    /**
     * Get the sale that owns the AccountTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class, 'reference_id', 'id');
    }

    /**
     * Get the sale that owns the AccountTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase_return(): BelongsTo
    {
        return $this->belongsTo(PurchaseReturn::class, 'reference_id', 'id');
    }

    /**
     * Get the sale that owns the AccountTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale_return(): BelongsTo
    {
        return $this->belongsTo(SaleReturn::class, 'reference_id', 'id');
    }
    
}
