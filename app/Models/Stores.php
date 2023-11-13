<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stores extends Model
{
    use HasFactory,SoftDeletes,  UniversalScopeTrait;
    

    protected $table = 'stores';
    protected $fillable = ['id','store_name','store_phone','business_size','store_location','type','store_supervisor',
    'renewal_date','domain','email','is_locked','phone','is_trial','created_at','deleted_at'];
    
    /**
     * Get the config that owns the Stores
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function config(): BelongsTo
    {
        return $this->belongsTo(Configuration::class, 'store_id', 'id');
    }
    /**
     * Get the user that owns the Stores
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'store_supervisor', 'id');
    }
}
