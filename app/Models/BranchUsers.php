<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchUsers extends Model
{
    use HasFactory;
    protected $table = 'branch_user';
    protected $fillable = [
        'branch_id','user_id','created_by','updated_by'
    ];
    public function branches()
    {
        return $this->hasOne(Branch::class,'branch_user','branch_id','id');
    }
    // public function users()
    // {
    //     return $this->belongsToMany(User::class,'branch_user','user_id');
    // }
    
}
