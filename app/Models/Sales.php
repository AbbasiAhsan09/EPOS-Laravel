<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sales extends Model
{
    use HasFactory , SoftDeletes, UniversalScopeTrait;
    protected $table = 'sales';
    protected $fillable = ['customer_id' , 'recieved','order_process_status','password' ,'net_total','created_at','updated_at', 'note'];

    /**
     * Get the user that owns the Sales
     *F
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Parties::class, 'customer_id', 'id');
    }

    /**
     * Get the user that owns the Sales
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    /**
     * Get all of the comments for the Sales
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function order_details(): HasMany
    {
        return $this->hasMany(SalesDetails::class, 'sale_id', 'id');
    }

    /**
     * Get all of the transaction for the Sales
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(OrderTransactions::class, 'order_id', 'id');
    }

    
}
