<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeyboardShortcut extends Model
{
    protected $table = 'keyboard_shortcuts';
    protected $fillable = [
        'description',
        'key',
        'action_type',
        'uri'
    ];
    use HasFactory, SoftDeletes;
}
