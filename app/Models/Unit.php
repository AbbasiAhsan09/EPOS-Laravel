<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        'description',
        'is_base',
        'is_active',
        'created_by',
        'unit_type_id',
        'conversion_unit_id',
        'default_conversion_factor',
        'pre_defined',
        'store_id',
    ];
    protected $casts = [
        'is_base' => 'boolean',
        'is_active' => 'boolean',
    ];


    public function unit_type()
    {
        return $this->belongsTo(UnitType::class, 'unit_type_id');
    } 

    public function conversion_unit()
    {
        return $this->belongsTo(Unit::class, 'conversion_unit_id')->with("conversion_unit");
    }

    use HasFactory, SoftDeletes, UniversalScopeTrait;
}
