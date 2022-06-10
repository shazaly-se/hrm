<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table="countries";
    protected $fillable = [
        'country_enName','country_arName','country_enNationality','country_arNationality'
    ];

}
