<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fields extends Model
{
   protected $table = 'fields';
   protected $fillable = ['name', 'added_by'];

   use HasFactory;

   

}
