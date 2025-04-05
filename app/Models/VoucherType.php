<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherType extends Model
{
    protected $table = 'voucher_types';
    protected $fillable = [
        "account_id",
        "store_id",
        "slug",
        "name",
        "description",
        "type",
        "show_head",
        "show_coa",
        "account_types",
        "account_reference_types",
        "is_bank_recieve"
    ];
    use HasFactory, SoftDeletes, UniversalScopeTrait;
}
