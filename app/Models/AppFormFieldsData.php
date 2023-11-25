<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppFormFieldsData extends Model
{
    protected $fillable = ['form_id','field_id','value','store_id','related_to'];
    use HasFactory;
}
