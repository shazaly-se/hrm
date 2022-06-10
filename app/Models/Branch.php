<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_name','address','zipcode','company_id','phone','email','created_by','updated_by'
    ];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_user', 'branch_id', 'user_id');
    } 
}
