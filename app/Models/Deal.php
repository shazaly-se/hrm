<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use HasFactory;
    protected $fillable = [
        'deal_name','pipeline','deal_stage','amount','close_date','deal_owner','deal_type','priority','company_id','company_timeline','contact_timeline','line_item','quantity','branch_id','created_by','updated_by',
    ];
}
