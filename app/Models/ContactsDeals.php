<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactsDeals extends Model
{
    use HasFactory;
    protected $fillable = [
        'contact_id','deal_id','branch_id','created_by','updated_by'
    ];
}
