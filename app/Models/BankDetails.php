<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id','bank_holder_name','bank_name','account_number','opening_balance','contact_number','bank_address','branch_id','created_by','updated_by'
    ];
}
