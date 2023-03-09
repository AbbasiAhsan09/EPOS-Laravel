<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $table = 'configurations';
    protected $fillable = ['app_title', 'logo','address','phone','ntn','ptn','show_ntn','show_ptn','inventory_tracking',
    'mutltiple_sales_order','start_date','contract_duration','renewed_on',
    'invoice_message', 'inv_dev_message','dev_contact','added_by','updated_by'];
    use HasFactory;
}
