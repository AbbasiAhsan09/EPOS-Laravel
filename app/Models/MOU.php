<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MOU extends Model
{
    protected $fillable = ['uom','base_unit','base_unit_value'];
    use HasFactory;
    use SoftDeletes;
    protected $table = 'mou';
}
