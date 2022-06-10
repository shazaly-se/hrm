<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    use HasFactory;
    protected $fillable = [
        'custom_field_name','type_id','module_id','branch_id','created_by','updated_by'
    ];
}
