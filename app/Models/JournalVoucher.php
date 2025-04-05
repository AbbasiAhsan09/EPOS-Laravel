<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalVoucher extends Model
{

    protected $table = 'journal_vouchers';
    protected $fillable = [
        'date',
        'account_id',
        'doc_no',
        'store_id',
        'user_id',
        'reference_no',
        'total_credit',
        'total_debit',
        'note',
    ];

    use HasFactory, SoftDeletes, UniversalScopeTrait;

    public function entries(): HasMany
    {
        return $this->hasMany(JournalVoucherDetail::class, 'journal_voucher_id', 'id');
    }


    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
