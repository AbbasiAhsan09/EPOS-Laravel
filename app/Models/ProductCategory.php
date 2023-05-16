<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
   
    use HasFactory;
    use SoftDeletes;

    /**
     * Get all of the categories for the ProductCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function field(): BelongsTo
    {
        return $this->BelongsTo(Fields::class, 'parent_cat', 'id');
    }
    protected $table = 'product_categories';
}
