<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Contact extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id','email','first_name','last_name','contact_owner_id','job_title','company_name','mobile_phone_number','phone_number','street_address',
        'landing_page_link','lifecycle_stage_id','no_of_repeats','created_by','updated_by'
    ];
    public function tasks()
    {
        return $this->hasOne(Task::class,'contact_id','id')->latestOfMany();
    }
    public function unassignedtasks()
    {
        return $this->hasOne(Task::class,'contact_id','id')->latestOfMany()->where('assigned_to_id','NULL');
    }
    public function agentTask()
    {
        $id = Auth::id();
        return $this->hasOne(Task::class,'contact_id','id')->latestOfMany()->where('assigned_to_id',$id);
    }
    public function revisionTask()
    {
        return $this->hasMany(Task::class,'contact_id','id');
    }
    public function deals()
    {
        return $this->hasOne(Task::class,'contact_id','id')->where('lead_status_id','10');
    }
    public function agentDeals()
    {
        $id = Auth::id();
        return $this->hasOne(Task::class,'contact_id','id')->where('assigned_to_id',$id)->where('lead_status_id','10');
    }
    // public function leadstatus()
    // {
    //     return $this->belongsTo(LeadStatus::class,'lead_status_id','id');
    // }
}
