<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppForms extends Model
{
    protected $fillable = ['name'];


    /**
     * Get all of the fields for the AppForms
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fields(): HasMany
    {
        return $this->hasMany(AppFormFields::class, 'form_id', 'id');
    }
   
    use HasFactory, UniversalScopeTrait;
}
