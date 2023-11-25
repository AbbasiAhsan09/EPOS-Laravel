<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppFormFieldsOptions extends Model
{
    protected $fillable = ['label','value','diabled','store_id','by_user'];
    use HasFactory, SoftDeletes;
}
