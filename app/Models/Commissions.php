<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commissions extends Model
{
    use HasFactory;
    protected $fillable = [
        'commission','date','property_id','tenant_id','branch_id','created_by','updated_by'
    ];
}
