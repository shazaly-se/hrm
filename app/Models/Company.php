<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    // ,SoftDeletes
    protected $fillable = [
        'name','logo','email','user_id','created_by','updated_by'
    ];
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
