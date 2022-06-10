<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveStatus extends Model
{
    use HasFactory;
    protected $table="leavesstatus";
    protected $fillable = [
        'status','created_by','updated_by'
    ];
}
