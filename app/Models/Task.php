<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id','contact_id','assigned_to_id','assigned_by_id','assigned_at','lead_status_id','project_id','comments','notes','email_subject','email_content',
        'log_email','email_description','call_outcome','call_description','contacted_datetime','task_title','due_datetime','type','priority','queue','task_note',
        'remainder','created_by','updated_by'       
    ];
    public function leadstatus()
    {
        return $this->belongsTo(LeadStatus::class,'lead_status_id','id');
    }
    public function users()
    {
        return $this->belongsTo(User::class,'assigned_to_id','id');
    }
    public function projects()
    {
        return $this->belongsTo(Project::class,'project_id','id');
    }
}
