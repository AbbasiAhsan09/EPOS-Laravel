<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabourWorkHistory extends Model
{
    protected $table = 'labour_work_histories';
    protected $fillable = [
        'labour_id',
        'doc_no',
        'start_date',
        'end_date',
        'is_ended',
        'is_paid',
        'paid_date',
        'other_charges',
        'bonus',
        'total',
        'net_total',
        'store_id',
        'user_id',
        'notes'
    ];


    /**
     * Get all of the items for the LabourWorkHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(LabourWorkHistoryItems::class, 'labour_work_history_id', 'id');
    }

    /**
     * Get the labour that owns the LabourWorkHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function labour(): BelongsTo
    {
        return $this->belongsTo(Labour::class, 'labour_id', 'id');
    }
    use HasFactory, SoftDeletes, UniversalScopeTrait;
}
