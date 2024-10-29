<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherEntry extends Model
{
    protected $table = 'voucher_entries';
    protected $fillable = [
        'voucher_id',
        'reference',
        'reference_type',
        'reference_id',
        'sale_id',
        'purchase_invoice_id',
        'description',
        'store_id',
        'amount'
    ];
    use HasFactory, SoftDeletes, UniversalScopeTrait;
}
