<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LifecycleStage extends Model
{
    use HasFactory;
    protected $fillable = [
        'name','branch_id','created_by','updated_by'
    ];
}
