<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppFormFields extends Model
{
    protected $fillable = ['form_id','label','name','datatype','type','required','store_id','by_user'];

    use HasFactory, SoftDeletes, UniversalScopeTrait;
}
