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
        'recorded_by'
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
}
