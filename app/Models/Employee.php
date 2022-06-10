<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name','contact','emirates_id','passport_number','nationality','email_address',
        'joining_date','visa_number','visa_expiry_date','labour_contract_number','labour_contract_expiry_date','visa','labour_contract','photo','emirates_id_file','passport',
        'user_id','branch_id','created_by','updated_by','status'
        // ,'visa_details','labour_contract','photo'
    ];

    public function designation()
    {
        return $this->hasOne(Designation::class,"designation_id");
    }
}
