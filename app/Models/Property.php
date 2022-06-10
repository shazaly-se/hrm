<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $fillable = [
        'name','tower_name','unit_number','floor_number','parking_slot','description','number_of_bedrooms','kitchen','hall','furnished','status','property_owner_id',
        'branch_id','created_by','updated_by'
    ];
}
