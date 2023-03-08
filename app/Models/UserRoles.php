<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRoles extends Model
{
    protected $table  = 'user_roles';
    protected $fillable = ['role_name','status'];
    use HasFactory, SoftDeletes;
   
}
