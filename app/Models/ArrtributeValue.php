<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArrtributeValue extends Model
{
    protected $table = 'product_arrtribute_values';
    use HasFactory;
    use SoftDeletes;
}
