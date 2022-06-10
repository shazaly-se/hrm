<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;
    protected $fillable = [
        'call_title','activity_date','activity_assigned_to','call_notes','call_outcome','transcript_available','call_duration','contact_id','created_by','updated_by'
    ];
}
