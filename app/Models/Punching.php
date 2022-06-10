<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Punching extends Model
{
    use HasFactory;
    protected $fillable = [
        'date','punch_in','punch_out','employee_id','branch_id','created_by','updated_by'
    ];
}