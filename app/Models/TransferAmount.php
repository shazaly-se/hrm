<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferAmount extends Model
{
    use HasFactory;
    protected $fillable = [
        'from_account','to_account','amount','date','reference','description','branch_id','created_by','updated_by'
    ];
}
