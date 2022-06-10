<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyOwner extends Model
{
    use HasFactory;
    protected $fillable = [
        'name','contact','emirates_id','passport_number','nationality','email_address','emirates_id_file','passport','branch_id','created_by','updated_by'
    ];
}
