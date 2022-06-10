<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFieldTypes extends Model
{
    use HasFactory;
    protected $fillable = [
        'custom_field_type','branch_id','created_by','updated_by'
    ];
}
