<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomaticAssigning extends Model
{
    use HasFactory;
    protected $fillable = [
        'admin_id','status','created_by','updated_by'
    ];
}
