<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyGroups extends Model
{
    protected $fillable = ['group_name'];
    use HasFactory;
}
