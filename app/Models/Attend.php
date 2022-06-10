<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attend extends Model
{
    use HasFactory;
    protected $table="attends";
    protected $fillable = [
        'employee_id','date','check_in','break_out','breack_in','check_out','total'
    ];
}
