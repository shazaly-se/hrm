<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id','application_date','leave_from_date','leave_to_date','number_of_days','reason','status','approved_datetime','approved_by','branch_id','created_by','updated_by'
    ];
    public function employee()
    {
        return $this->belongsto(Employee::class);
    }
    public function users()
    {
        return $this->belongsto(User::class,'approved_by','id');
    }
}
 