<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalVoucherDetail extends Model
{
    protected $table = 'journal_voucher_details';
    protected $fillable = [
        'journal_voucher_id',
        'account_id',
        'store_id',
        'reference_no',
        'debit',
        'credit',
        'description',
        'mode'
    ];
    use HasFactory, SoftDeletes, UniversalScopeTrait;


    /**
     * Get the account that owns the JournalVoucherDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
