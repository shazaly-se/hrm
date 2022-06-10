<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveInfo extends Model
{
    use HasFactory;
    protected $table="leavesinfos";
    protected $fillable = [
        'employee_id','leave_type','leave_status','number_of_date','start_date','end_date','reason'
    ];
}
