<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Labour extends Model
{
    protected $table = 'labours';
    protected $fillable = ['name','phone','address','description','store_id'];

    use HasFactory, SoftDeletes, UniversalScopeTrait;
}
