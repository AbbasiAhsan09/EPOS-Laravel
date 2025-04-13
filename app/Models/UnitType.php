<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'created_by',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];
    protected $table = 'unit_types';
    use HasFactory, SoftDeletes;
}
