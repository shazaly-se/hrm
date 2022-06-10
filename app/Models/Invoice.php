<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id','issue_date','due_date','invoice_number','category','reference_number','discount_apply','sku','branch_id','created_by','updated_by',
    ];
}