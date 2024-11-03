<?php

namespace App\Models;

use App\Http\Trait\UniversalScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $table = 'configurations';
    protected $fillable = ['app_title', 'logo','address','phone','ntn','ptn','show_ntn','show_ptn','inventory_tracking',
    'mutltiple_sales_order','start_date','contract_duration','store_id','order_processing','order_processing_template',
    'invoice_message','allow_inventory_check','allow_low_inventory','added_by','show_tp_in_order_form',
    'use_accounting_module','ui',
    'invoice_logo','invoice_name'];
    use UniversalScopeTrait, HasFactory;
}
