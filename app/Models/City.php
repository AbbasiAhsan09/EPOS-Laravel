<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{   protected $fillable = ['country_id','state_id','name'];
    use HasFactory, SoftDeletes;
}
