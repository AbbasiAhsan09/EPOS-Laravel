<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    protected $table = 'vouchers';
    protected $fillable = [
        'doc_no',
        'store_id',
        'user_id',
        'account_id',
        'date',
        'note',
        'total',
        'voucher_type_id',
        'mode',
        'reference_no',
        'account_from_id',
        'reference',
        'account_from_id'
    ];


    /**
     * Get all of the entries for the Voucher
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries(): HasMany
    {
        return $this->hasMany(VoucherEntry::class, 'voucher_id', 'id');
    }

    /**
     * Get the account that owns the Voucher
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function account_from(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_from_id', 'id');
    }

    /**
     * Get the user that owns the Voucher
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    use HasFactory, SoftDeletes, UniversalScopeTrait;
}
