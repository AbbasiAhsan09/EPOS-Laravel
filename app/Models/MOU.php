<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MOU extends Model
{
    protected $fillable = ['uom','base_unit','base_unit_value'];
    use HasFactory,SoftDeletes,UniversalScopeTrait;
    protected $table = 'mou';
}
