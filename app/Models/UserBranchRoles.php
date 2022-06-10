<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBranchRoles extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id','role_id','branch_id','created_by','updated_by'
    ];
}
