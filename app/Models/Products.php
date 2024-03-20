<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Products extends Model
{
    protected $fillable = ['mrp','tp','taxes','discount','check_inv'];
    protected $appends = ['fullProductName'];
     /**
     * Get the user that owns the ProductCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uoms(): BelongsTo
    {
        return $this->belongsTo(MOU::class, 'uom', 'id');
    }

    public function getFullProductNameAttribute(){

        if(Auth::check() && Auth::user()->storeConfig){
            $productPattern = (Auth::user()->storeConfig->product_pattern);
            return(eval('return '. $productPattern));
        }
        
        return $this->name;
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
