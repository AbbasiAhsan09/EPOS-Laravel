<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Products extends Model
{
    protected $fillable = ['mrp','tp','taxes','discount'];
     /**
     * Get the user that owns the ProductCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uoms(): BelongsTo
    {
        return $this->belongsTo(MOU::class, 'uom', 'id');
    }

    /**
     * Get the user that owns the ProductCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categories(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category', 'id');
    }
    use HasFactory,SoftDeletes, UniversalScopeTrait;
    protected $table = 'products';
    
}
