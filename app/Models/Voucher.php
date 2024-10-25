<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'reference'
    ];
    use HasFactory, SoftDeletes, UniversalScopeTrait;
}
