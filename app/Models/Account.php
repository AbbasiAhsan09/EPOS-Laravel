<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    protected $table = 'accounts';
    protected $fillable = [
        'title','pre_defined','type','store_id','description',
        'color_code','reference_id','reference_type','opening_balance',
        'account_number','parent_id','current_balance'
    ];

    use HasFactory, SoftDeletes, UniversalScopeTrait;
}
