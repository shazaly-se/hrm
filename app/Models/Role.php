<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\role_has_permissions;

class Role extends Model
{
    use HasFactory;
    protected $table="roles";
    protected $fillable = [
        'name','company_id','created_by','updated_by'
    ];

}
