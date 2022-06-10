<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taxes extends Model
{
    use HasFactory;
    protected $fillable = [
        'tax_rate_name','tax_rate_percentage','branch_id','created_by','updated_by'
    ];
}
