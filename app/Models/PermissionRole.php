<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionRole extends Model
{
    use HasFactory;
    protected $table="permission_role";
    protected $fillable = [
        'role_id','permission_id','created_by','updated_by','updated_at','created_at'
    ];
}
