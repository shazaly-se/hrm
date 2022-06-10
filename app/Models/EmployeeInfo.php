<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeInfo extends Model
{
    use HasFactory;
    protected $table="employeeinfos";
    protected $fillable = [
        'fullname','birthdate','joineddate','gender','departmentselected','designationselected',
        'employeephone','employeeEmail','employeeAddress','labourContractExpiry',
        'passportno','passportExpiryDate','nationalityselected','religion','maritialStatus','numberofChildren',
        'emergencycontactname','emergencycontactrelationship','emergencycontactphone','employeebankname','employeebankacc'
        ,'employeebankiban','employeehomeadd','employeehomephone','created_at','updated_at'
    ];
}
