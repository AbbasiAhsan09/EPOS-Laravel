<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fields extends Model
{
   protected $table = 'fields';
   protected $fillable = ['name', 'added_by'];

   use HasFactory, UniversalScopeTrait, SoftDeletes;

   

}
