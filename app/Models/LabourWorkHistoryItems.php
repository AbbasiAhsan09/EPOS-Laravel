<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabourWorkHistoryItems extends Model
{
    protected $table = 'labour_work_history_items';
    
    protected $fillable = [
        'labour_work_history_id',
        'date',
        'rate',
        'description',
        'qty',
        'total'
    ];

    use HasFactory, UniversalScopeTrait, SoftDeletes;
}
