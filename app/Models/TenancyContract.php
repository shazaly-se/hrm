<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenancyContract extends Model
{
    use HasFactory;
    protected $fillable = [
        'access','contact','status','contract_date','start_date','expiry_date','security_check','duration','amount','payment_method','expiry_date','tasdeeq_username',
        'tasdeeq_password','sewerage_premise_id','sewerage_username','sewerage_password','fewa_premise_id','fewa_username','fewa_password','notes','property_id','tenant_id',
        'branch_id','created_by','updated_by'
    ];
}
