<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    protected $fillable = ['is_opnening_stock', 'item_id', 'stock_qty', 'wght_cost'];
    use HasFactory, SoftDeletes;
}
