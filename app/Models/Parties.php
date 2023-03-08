<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parties extends Model
{
    use HasFactory , SoftDeletes;
    /**
     * Get the user that owns the Parties
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groups(): BelongsTo
    {
        return $this->belongsTo(PartyGroups::class, 'group_id', 'id');
    }
}
