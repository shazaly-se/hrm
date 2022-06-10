<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Company;
use App\Models\Branch;
use App\Models\BranchUsers;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use App\Models\Task;
use App\Models\LeadStatus;
use App\Models\AutomaticAssigning;
use App\Models\Project;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // return $request->all();
        // filter by inputs
        $assigned_to_id = $request->assigned_to_id;
        // dd($assigned_to_id);
        $assigned_to_id = array_map('intval', $assigned_to_id);
        // $assigned_to_id = '';
        $created_at_start_date = $request->created_at_start_date;
        $created_at_end_date = $request->created_at_end_date; 
        $last_activity_start_date = $request->last_activity_start_date;
        $last_activity_end_date = $request->last_activity_end_date; 
        $lead_status_id = $request->lead_status_id; 
        $lead_status_id = array_map('intval', $lead_status_id);
        $project_id = $request->project_id; 
        $project_id = array_map('intval', $project_id);
        //
        $leadstatus = LeadStatus::where('id', '!=', 9)->get(array("lead_statuses.id as value","lead_statuses.name as label"));
        $salesAgents = '';
        $projects = '';
        $contact = '';
        $id = Auth::id();
        $automaticStatus = AutomaticAssigning::where('admin_id',$id)->first();
        // checking whether it is superadmin
        $companyId = Company::where('user_id',$id)->first();     
        if($companyId == NULL)
        {
            //checking whether it is sales agent
            $tasks = Task::where('assigned_to_id',$id)->pluck('contact_id');
            if($tasks->isEmpty())
            {
                // Branch Admin
                $branch_id = BranchUsers::where('user_id',$id)->first();
                //filter based on lead status
                $contact = Contact::where('branch_id',$branch_id->branch_id)->with(['tasks' => function($q) use($project_id, $assigned_to_id, $lead_status_id, $last_activity_start_date, $last_activity_end_date){
                    if($project_id)
                    {
                        $q = $q->whereIn('project_id',$project_id);
                    }
                    // dd($assigned_to_id);
                    if($assigned_to_id)
                    {
                        $q = $q->whereIn('assigned_to_id',$assigned_to_id);
                    }
                    if($lead_status_id)
                    {
                        $q = $q->whereIn('lead_status_id',$lead_status_id);
                    }
                    if($last_activity_start_date)
                    {
                        $last_activity_start_date = $last_activity_start_date.' 00:00:00';
                        $q = $q->where('updated_at','>=',$last_activity_start_date);
                    }
                    if($last_activity_end_date)
                    {
                        $last_activity_end_date = $last_activity_end_date.' 23:59:59';
                        $q = $q->where('updated_at','<=',$last_activity_end_date);
                    }

                    $q->with('leadstatus','users','projects');
                }]);
                //filter by input
                if($created_at_start_date)
                {
                    $created_at_start_date = $created_at_start_date.' 00:00:00';
                    $contact = $contact->where('created_at','>=',$created_at_start_date);
                }
                if($created_at_end_date)
                {
                    $created_at_end_date = $created_at_end_date.' 23:59:59';
                    $contact = $contact->where('created_at','<=',$created_at_end_date);
                }
                $contact = $contact->get();
                //
                // dd($contact);
                $salesAgentsId = RoleUser::where('role_id','4')->pluck('user_id'); 
                $salesAgentsId = BranchUsers::whereIn('user_id',$salesAgentsId)->whereIn('branch_id',$branch_id)->pluck('user_id');
                $salesAgents = User::whereIn('id',$salesAgentsId)->get(array("users.id as value","users.name as label"));
                $projects = Project::where([
                    ['branch_id',$branch_id->branch_id],
                    ['status','active']
                    ])->get(array("projects.id as value","projects.project_name as label"));
            }
            else
            {
                //Sales Agent
                $branch_id = BranchUsers::where('user_id',$id)->first();
                $projects_id = Task::where('assigned_to_id',$id)->pluck('project_id');
                $projects = Project::whereIn('id',$projects_id)->where('status','active')->get(array("projects.id as value","projects.project_name as label"));
            }
       }
       else
       {
           // Company Super Admin
           $branch_id = Branch::where('company_id',$companyId->id)->pluck('id');
           $roleUser = RoleUser::where('role_id','4')->pluck('user_id');
           $salesAgents = User::whereIn('id',$roleUser)->get(array("users.id as value","users.name as label"));
           $projects = Project::whereIn('branch_id',$branch_id)->where('status','active')->get(array("projects.id as value","projects.project_name as label"));
       }
        return response()->json([
        "success" => true,
        "message" => "Contact List",
        "data" => $contact,
        "salesAgent" => $salesAgents,
        "leadstatus" => $leadstatus,
        "automaticStatus" => $automaticStatus,
        'projects' => $projects,
        ]);
    }
    public function myContacts(Request $request)
    {
        // filter by inputs
        $assigned_to_id = $request->assigned_to_id;
        $assigned_to_id = array_map('intval', $assigned_to_id);
        $created_at_start_date = $request->created_at_start_date;
        $created_at_end_date = $request->created_at_end_date; 
        $last_activity_start_date = $request->last_activity_start_date;
        $last_activity_end_date = $request->last_activity_end_date; 
        $lead_status_id = $request->lead_status_id; 
        $lead_status_id = array_map('intval', $lead_status_id);
        $project_id = $request->project_id; 
        $project_id = array_map('intval', $project_id);
        //
        $leadstatus = LeadStatus::where('id', '!=', 9)->get(array("lead_statuses.id as value","lead_statuses.name as label"));
        $salesAgents = '';
        $projects = '';
        $contact = '';
        $id = Auth::id();
        $automaticStatus = AutomaticAssigning::where('admin_id',$id)->first();
        // checking whether it is superadmin
        $companyId = Company::where('user_id',$id)->first();     
        if($companyId == NULL)
        {
            //checking whether it is sales agent
            $tasks = Task::where('assigned_to_id',$id)->pluck('contact_id');
            if($tasks->isEmpty())
            {
                // Branch Admin
                $branch_id = BranchUsers::where('user_id',$id)->first();
                //filter based on lead status
                $contact = Contact::where('branch_id',$branch_id->branch_id)->whereIn('id',$tasks)->with(['agentTask' => function($q) use($project_id, $assigned_to_id, $lead_status_id, $last_activity_start_date, $last_activity_end_date){
                    if($assigned_to_id)
                    {
                        $q = $q->whereIn('assigned_to_id',$assigned_to_id);
                    }
                    if($project_id)
                    {
                        $q = $q->whereIn('project_id',$project_id);
                    }
                    if($lead_status_id)
                    {
                        $q = $q->whereIn('lead_status_id',$lead_status_id);
                    }
                    if($last_activity_start_date)
                    {
                        $last_activity_start_date = $last_activity_start_date.' 00:00:00';
                        $q = $q->where('updated_at','>=',$last_activity_start_date);
                    }
                    if($last_activity_end_date)
                    {
                        $last_activity_end_date = $last_activity_end_date.' 23:59:59';
                        $q = $q->where('updated_at','<=',$last_activity_end_date);
                    }
                    $q->with('leadstatus','users','projects');
                }]);
                //filter by input
                if($created_at_start_date)
                {
                    $created_at_start_date = $created_at_start_date.' 00:00:00';
                    $contact = $contact->where('created_at','>=',$created_at_start_date);
                }
                if($created_at_end_date)
                {
                    $created_at_end_date = $created_at_end_date.' 23:59:59';
                    $contact = $contact->where('created_at','<=',$created_at_end_date);
                }
                $contact = $contact->get();
                //
                $salesAgentsId = RoleUser::where('role_id','4')->pluck('user_id'); 
                $salesAgentsId = BranchUsers::whereIn('user_id',$salesAgentsId)->whereIn('branch_id',$branch_id)->pluck('user_id');
                $salesAgents = User::whereIn('id',$salesAgentsId)->get(array("users.id as value","users.name as label"));
                $projects = Project::where([
                    ['branch_id',$branch_id->branch_id],
                    ['status','active']
                    ])->get(array("projects.id as value","projects.project_name as label"));
            }
            else
            {
                //Sales Agent
                $branch_id = BranchUsers::where('user_id',$id)->first();
                    //all contacts
                    $contact = Contact::where('branch_id',$branch_id->branch_id)->whereIn('id',$tasks)->with(['agentTask' => function($q) use($project_id, $assigned_to_id, $lead_status_id, $last_activity_start_date, $last_activity_end_date){
                        if($assigned_to_id)
                    {
                        $q = $q->whereIn('assigned_to_id',$assigned_to_id);
                    }
                    if($project_id)
                    {
                        $q = $q->whereIn('project_id',$project_id);
                    }
                    if($lead_status_id)
                    {
                        $q = $q->whereIn('lead_status_id',$lead_status_id);
                    }
                    if($last_activity_start_date)
                    {
                        $last_activity_start_date = $last_activity_start_date.' 00:00:00';
                        $q = $q->where('updated_at','>=',$last_activity_start_date);
                    }
                    if($last_activity_end_date)
                    {
                        $last_activity_end_date = $last_activity_end_date.' 23:59:59';
                        $q = $q->where('updated_at','<=',$last_activity_end_date);
                    }
                    $q->with('leadstatus','users','projects');
                }]);
                //filter by input
                if($created_at_start_date)
                {
                    $created_at_start_date = $created_at_start_date.' 00:00:00';
                    $contact = $contact->where('created_at','>=',$created_at_start_date);
                }
                if($created_at_end_date)
                {
                    $created_at_end_date = $created_at_end_date.' 23:59:59';
                    $contact = $contact->where('created_at','<=',$created_at_end_date);
                }
                $contact = $contact->get();
                //
                $projects_id = Task::where('assigned_to_id',$id)->pluck('project_id');
                $projects = Project::whereIn('id',$projects_id)->where('status','active')->get(array("projects.id as value","projects.project_name as label"));
            }
       }
       else
       {
           // Company Super Admin
           $branch_id = Branch::where('company_id',$companyId->id)->pluck('id');
                    //all contacts
                    $contact = Contact::where('created_by', $id)->with(['tasks' => function($q) use($project_id, $assigned_to_id, $lead_status_id, $last_activity_start_date, $last_activity_end_date){
                    if($assigned_to_id)
                    {
                        $q = $q->whereIn('assigned_to_id',$assigned_to_id);
                    }
                    if($project_id)
                    {
                        $q = $q->whereIn('project_id',$project_id);
                    }
                    if($lead_status_id)
                    {
                        $q = $q->whereIn('lead_status_id',$lead_status_id);
                    }
                    if($last_activity_start_date)
                    {
                        $last_activity_start_date = $last_activity_start_date.' 00:00:00';
                        $q = $q->where('updated_at','>=',$last_activity_start_date);
                    }
                    if($last_activity_end_date)
                    {
                        $last_activity_end_date = $last_activity_end_date.' 23:59:59';
                        $q = $q->where('updated_at','<=',$last_activity_end_date);
                    }
                    $q->with('leadstatus','users','projects');
                }]);
                //filter by input
                if($created_at_start_date)
                {
                    $created_at_start_date = $created_at_start_date.' 00:00:00';
                    $contact = $contact->where('created_at','>=',$created_at_start_date);
                }
                if($created_at_end_date)
                {
                    $created_at_end_date = $created_at_end_date.' 23:59:59';
                    $contact = $contact->where('created_at','<=',$created_at_end_date);
                }
                $contact = $contact->get();
                //
           $roleUser = RoleUser::where('role_id','4')->pluck('user_id');
           $salesAgents = User::whereIn('id',$roleUser)->get(array("users.id as value","users.name as label"));
           $projects = Project::whereIn('branch_id',$branch_id)->where('status','active')->get(array("projects.id as value","projects.project_name as label"));
       }
        return response()->json([
        "success" => true,
        "message" => "Contact List",
        "data" => $contact,
        "salesAgent" => $salesAgents,
        "leadstatus" => $leadstatus,
        "automaticStatus" => $automaticStatus,
        'projects' => $projects,
        ]);  
    }
    public function filter(Request $request, $status)
    {
        // dd($status);
        // filter by inputs
        $assigned_to_id = $request->assigned_to_id;
        $assigned_to_id = array_map('intval', $assigned_to_id);
        $created_at_start_date = $request->created_at_start_date;
        $created_at_end_date = $request->created_at_end_date; 
        $last_activity_start_date = $request->last_activity_start_date;
        $last_activity_end_date = $request->last_activity_end_date; 
        $lead_status_id = $request->lead_status_id; 
        $lead_status_id = array_map('intval', $lead_status_id);
        $project_id = $request->project_id; 
        $project_id = array_map('intval', $lead_status_id);
        //
        $leadstatus = LeadStatus::where('id', '!=', 9)->get(array("lead_statuses.id as value","lead_statuses.name as label"));
        $salesAgents = '';
        $projects = '';
        $contact = '';
        $id = Auth::id();
        $automaticStatus = AutomaticAssigning::where('admin_id',$id)->first();
        // checking whether it is superadmin
        $companyId = Company::where('user_id',$id)->first();     
        if($companyId == NULL)
        {
            //checking whether it is sales agent
            $tasks = Task::where('assigned_to_id',$id)->pluck('contact_id');
            // dd()
            if($tasks->isEmpty())
            {
                // Branch Admin
                $branch_id = BranchUsers::where('user_id',$id)->first();
                // dd($branch_id);
                //filter based on lead status
               if($status == '0')
                {
                    // dd('hi');
                    // dd('hi');
                    //interested
                    $status = [2,3,4,6,7,8];
                    $contact = Contact::where('branch_id',$branch_id->branch_id)->whereHas('tasks', function ($query) use ($project_id, $status, $assigned_to_id, $lead_status_id, $last_activity_start_date, $last_activity_end_date) { 
                        $query->whereIn('lead_status_id',$status);
                        if($assigned_to_id)
                        {
                            $query = $query->whereIn('assigned_to_id',$assigned_to_id);
                        }
                        if($project_id)
                        {
                            $query = $query->whereIn('project_id',$project_id);
                        }
                        if($lead_status_id)
                        {
                            $query = $query->whereIn('lead_status_id',$lead_status_id);
                        }
                        if($last_activity_start_date)
                        {
                            $last_activity_start_date = $last_activity_start_date.' 00:00:00';
                            $query = $query->where('updated_at','>=',$last_activity_start_date);
                        }
                        if($last_activity_end_date)
                        {
                            $last_activity_end_date = $last_activity_end_date.' 23:59:59';
                            $query = $query->where('updated_at','<=',$last_activity_end_date);
                        }
                    })->with(['tasks' => function ($query) {
                        $query->with('leadstatus','users','projects');
                    }]);
                //filter by input
                if($created_at_start_date)
                {
                    $created_at_start_date = $created_at_start_date.' 00:00:00';
                    $contact = $contact->where('created_at','>=',$created_at_start_date);
                }
                if($created_at_end_date)
                {
                    $created_at_end_date = $created_at_end_date.' 23:59:59';
                    $contact = $contact->where('created_at','<=',$created_at_end_date);
                }
                $contact = $contact->get();
                //
                }
                elseif($status == '9')
                {
                    //unassigned
                    $contact = Contact::where('branch_id',$branch_id->branch_id)->whereHas('tasks', function ($query) use ($project_id, $status, $assigned_to_id, $lead_status_id, $last_activity_start_date, $last_activity_end_date) { 
                        $query->where('assigned_to_id',null);
                        if($assigned_to_id)
                        {
                            $query = $query->whereIn('assigned_to_id',$assigned_to_id);
                        }
                        if($project_id)
                        {
                            $query = $query->whereIn('project_id',$project_id);
                        }
                        if($lead_status_id)
                        {
                            $query = $query->whereIn('lead_status_id',$lead_status_id);
                        }
                        if($last_activity_start_date)
                        {
                            $last_activity_start_date = $last_activity_start_date.' 00:00:00';
                            $query = $query->where('updated_at','>=',$last_activity_start_date);
                        }
                        if($last_activity_end_date)
                        {
                            $last_activity_end_date = $last_activity_end_date.' 23:59:59';
                            $query = $query->where('updated_at','<=',$last_activity_end_date);
                        }
                    })->with(['tasks' => function ($query) {
                        $query->with('leadstatus','users','projects');
                    }]);
                    //filter by input
                if($created_at_start_date)
                {
                    $created_at_start_date = $created_at_start_date.' 00:00:00';
                    $contact = $contact->where('created_at','>=',$created_at_start_date);
                }
                if($created_at_end_date)
                {
                    $created_at_end_date = $created_at_end_date.' 23:59:59';
                    $contact = $contact->where('created_at','<=',$created_at_end_date);
                }
                $contact = $contact->get();
                //
                }
                else
                {
                    // contacts by lead status
                    $contact = Contact::where('branch_id',$branch_id->branch_id)->whereHas('tasks', function ($query) use ($project_id, $status, $assigned_to_id, $lead_status_id, $last_activity_start_date, $last_activity_end_date) { 
                        $query->where('lead_status_id', $status);
                        if($assigned_to_id)
                        {
                            $query = $query->whereIn('assigned_to_id',$assigned_to_id);
                        }
                        if($project_id)
                    {
                        $query = $query->whereIn('project_id',$project_id);
                    }
                        if($lead_status_id)
                        {
                            $query = $query->whereIn('lead_status_id',$lead_status_id);
                        }
                        if($last_activity_start_date)
                        {
                            $last_activity_start_date = $last_activity_start_date.' 00:00:00';
                            $query = $query->where('updated_at','>=',$last_activity_start_date);
                        }
                        if($last_activity_end_date)
                        {
                            $last_activity_end_date = $last_activity_end_date.' 23:59:59';
                            $query = $query->where('updated_at','<=',$last_activity_end_date);
                        }
                    })->with(['tasks' => function ($query) {
                        $query->with('leadstatus','users','projects');
                    }]);
                    //filter by input
                if($created_at_start_date)
                {
                    $created_at_start_date = $created_at_start_date.' 00:00:00';
                    $contact = $contact->where('created_at','>=',$created_at_start_date);
                }
                if($created_at_end_date)
                {
                    $created_at_end_date = $created_at_end_date.' 23:59:59';
                    $contact = $contact->where('created_at','<=',$created_at_end_date);
                }
                $contact = $contact->get();
                //
                }
                
                $salesAgentsId = RoleUser::where('role_id','4')->pluck('user_id'); 
                $salesAgentsId = BranchUsers::whereIn('user_id',$salesAgentsId)->whereIn('branch_id',$branch_id)->pluck('user_id');
                $salesAgents = User::whereIn('id',$salesAgentsId)->get(array("users.id as value","users.name as label"));
                $projects = Project::where([
                    ['branch_id',$branch_id->branch_id],
                    ['status','active']
                    ])->get(array("projects.id as value","projects.project_name as label"));
            }
            else
            {
                //Sales Agent
                $branch_id = BranchUsers::where('user_id',$id)->first();
                if($status == '0')
                {
                    //interested
                    $status = [2,3,4,6,7,8];
                    $contact = Contact::where('branch_id',$branch_id->branch_id)->whereIn('id',$tasks)->whereHas('agentTask', function ($query) use ($project_id, $status, $assigned_to_id, $lead_status_id, $last_activity_start_date, $last_activity_end_date) { 
                        $query->whereIn('lead_status_id',$status);
                        if($assigned_to_id)
                        {
                            $query = $query->whereIn('assigned_to_id',$assigned_to_id);
                        }
                        if($project_id)
                    {
                        $query = $query->whereIn('project_id',$project_id);
                    }
                        if($lead_status_id)
                        {
                            $query = $query->whereIn('lead_status_id',$lead_status_id);
                        }
                        if($last_activity_start_date)
                        {
                            $last_activity_start_date = $last_activity_start_date.' 00:00:00';
                            $query = $query->where('updated_at','>=',$last_activity_start_date);
                        }
                        if($last_activity_end_date)
                        {
                            $last_activity_end_date = $last_activity_end_date.' 23:59:59';
                            $query = $query->where('updated_at','<=',$last_activity_end_date);
                        }
                    })->with(['tasks' => function ($query) {
                        $query->with('leadstatus','users','projects');
                    }]);
                    //filter by input
                if($created_at_start_date)
                {
                    $created_at_start_date = $created_at_start_date.' 00:00:00';
                    $contact = $contact->where('created_at','>=',$created_at_start_date);
                }
                if($created_at_end_date)
                {
                    $created_at_end_date = $created_at_end_date.' 23:59:59';
                    $contact = $contact->where('created_at','<=',$created_at_end_date);
                }
                $contact = $contact->get();
                //
                }
                elseif($status == '9')
                {
                    //unassigned
                    $contact = Contact::where('branch_id',$branch_id->branch_id)->whereHas('tasks', function ($query) use ($project_id, $status, $assigned_to_id, $lead_status_id, $last_activity_start_date, $last_activity_end_date) { 
                        $query->where('assigned_to_id',null);
                        if($assigned_to_id)
                        {
                            $query = $query->whereIn('assigned_to_id',$assigned_to_id);
                        }
                        if($project_id)
                    {
                        $query = $query->whereIn('project_id',$project_id);
                    }
                        if($lead_status_id)
                        {
                            $query = $query->whereIn('lead_status_id',$lead_status_id);
                        }
                        if($last_activity_start_date)
                        {
                            $last_activity_start_date = $last_activity_start_date.' 00:00:00';
                            $query = $query->where('updated_at','>=',$last_activity_start_date);
                        }
                        if($last_activity_end_date)
                        {
                            $last_activity_end_date = $last_activity_end_date.' 23:59:59';
                            $query = $query->where('updated_at','<=',$last_activity_end_date);
                        }
                    })->with(['tasks' => function ($query) {
                        $query->with('leadstatus','users','projects');
                    }]);
                     //filter by input
                if($created_at_start_date)
                {
                    $created_at_start_date = $created_at_start_date.' 00:00:00';
                    $contact = $contact->where('created_at','>=',$created_at_start_date);
                }
                if($created_at_end_date)
                {
                    $created_at_end_date = $created_at_end_date.' 23:59:59';
                    $contact = $contact->where('created_at','<=',$created_at_end_date);
                }
                $contact = $contact->get();
                //
                }
                else
                {
                    // contacts by lead status
                    $contact = Contact::where('branch_id',$branch_id->branch_id)->whereIn('id',$tasks)->whereHas('agentTask', function ($query) use ($project_id, $status, $assigned_to_id, $lead_status_id, $last_activity_start_date, $last_activity_end_date) { 
                        $query->where('lead_status_id', $status);
                        if($assigned_to_id)
                        {
                            $query = $query->whereIn('assigned_to_id',$assigned_to_id);
                        }
                        if($project_id)
                    {
                        $query = $query->whereIn('project_id',$project_id);
                    }
                        if($lead_status_id)
                        {
                            $query = $query->whereIn('lead_status_id',$lead_status_id);
                        }
                        if($last_activity_start_date)
                        {
                            $last_activity_start_date = $last_activity_start_date.' 00:00:00';
                            $query = $query->where('updated_at','>=',$last_activity_start_date);
                        }
                        if($last_activity_end_date)
                        {
                            $last_activity_end_date = $last_activity_end_date.' 23:59:59';
                            $query = $query->where('updated_at','<=',$last_activity_end_date);
                        }
                    })->with(['tasks' => function ($query) {
                        $query->with('leadstatus','users','projects');
                    }]);
                     //filter by input
                if($created_at_start_date)
                {
                    $created_at_start_date = $created_at_start_date.' 00:00:00';
                    $contact = $contact->where('created_at','>=',$created_at_start_date);
                }
                if($created_at_end_date)
                {
                    $created_at_end_date = $created_at_end_date.' 23:59:59';
                    $contact = $contact->where('created_at','<=',$created_at_end_date);
                }
                $contact = $contact->get();
                //
                }
                $projects_id = Task::where('assigned_to_id',$id)->pluck('project_id');
                $projects = Project::whereIn('id',$projects_id)->where('status','active')->get(array("projects.id as value","projects.project_name as label"));
            }
       }
       else
       {
           // Company Super Admin
           $branch_id = Branch::where('company_id',$companyId->id)->pluck('id');
            if($status == '0')
                {
                    //interested
                    $status = [2,3,4,6,7,8];
                    $contact = Contact::where('created_by', $id)->whereHas('tasks', function ($query) use ($project_id, $status, $assigned_to_id, $lead_status_id, $last_activity_start_date, $last_activity_end_date) { 
                        $query->whereIn('lead_status_id',$status);
                        if($assigned_to_id)
                        {
                            $query = $query->whereIn('assigned_to_id',$assigned_to_id);
                        }
                        if($project_id)
                    {
                        $query = $query->whereIn('project_id',$project_id);
                    }
                        if($lead_status_id)
                        {
                            $query = $query->whereIn('lead_status_id',$lead_status_id);
                        }
                        if($last_activity_start_date)
                        {
                            $last_activity_start_date = $last_activity_start_date.' 00:00:00';
                            $query = $query->where('updated_at','>=',$last_activity_start_date);
                        }
                        if($last_activity_end_date)
                        {
                            $last_activity_end_date = $last_activity_end_date.' 23:59:59';
                            $query = $query->where('updated_at','<=',$last_activity_end_date);
                        }
                    })->with(['tasks' => function ($query) {
                        $query->with('leadstatus','users','projects');
                    }]);
                    //filter by input
                if($created_at_start_date)
                {
                    $created_at_start_date = $created_at_start_date.' 00:00:00';
                    $contact = $contact->where('created_at','>=',$created_at_start_date);
                }
                if($created_at_end_date)
                {
                    $created_at_end_date = $created_at_end_date.' 23:59:59';
                    $contact = $contact->where('created_at','<=',$created_at_end_date);
                }
                $contact = $contact->get();
                //
                }
                elseif($status == '9')
                {
                    //unassigned
                    $contact = Contact::where('created_by',$id)->whereHas('tasks', function ($query) use ($project_id, $status, $assigned_to_id, $lead_status_id, $last_activity_start_date, $last_activity_end_date){ 
                        $query->where('assigned_to_id',null);
                        if($assigned_to_id)
                        {
                            $query = $query->whereIn('assigned_to_id',$assigned_to_id);
                        }
                        if($project_id)
                    {
                        $query = $query->whereIn('project_id',$project_id);
                    }
                        if($lead_status_id)
                        {
                            $query = $query->whereIn('lead_status_id',$lead_status_id);
                        }
                        if($last_activity_start_date)
                        {
                            $last_activity_start_date = $last_activity_start_date.' 00:00:00';
                            $query = $query->where('updated_at','>=',$last_activity_start_date);
                        }
                        if($last_activity_end_date)
                        {
                            $last_activity_end_date = $last_activity_end_date.' 23:59:59';
                            $query = $query->where('updated_at','<=',$last_activity_end_date);
                        }
                    })->with(['tasks' => function ($query) {
                        $query->with('leadstatus','users','projects');
                    }]);
                     //filter by input
                if($created_at_start_date)
                {
                    $created_at_start_date = $created_at_start_date.' 00:00:00';
                    $contact = $contact->where('created_at','>=',$created_at_start_date);
                }
                if($created_at_end_date)
                {
                    $created_at_end_date = $created_at_end_date.' 23:59:59';
                    $contact = $contact->where('created_at','<=',$created_at_end_date);
                }
                $contact = $contact->get();
                //
                }
                else
                {
                    // contacts by lead status
                    $contact = Contact::where('created_by', $id)->whereHas('tasks', function ($query) use ($project_id, $status, $assigned_to_id, $lead_status_id, $last_activity_start_date, $last_activity_end_date) { 
                        $query->where('lead_status_id', $status);
                        if($assigned_to_id)
                        {
                            $query = $query->whereIn('assigned_to_id',$assigned_to_id);
                        }
                        if($project_id)
                    {
                        $query = $query->whereIn('project_id',$project_id);
                    }
                        if($lead_status_id)
                        {
                            $query = $query->whereIn('lead_status_id',$lead_status_id);
                        }
                        if($last_activity_start_date)
                        {
                            $last_activity_start_date = $last_activity_start_date.' 00:00:00';
                            $query = $query->where('updated_at','>=',$last_activity_start_date);
                        }
                        if($last_activity_end_date)
                        {
                            $last_activity_end_date = $last_activity_end_date.' 23:59:59';
                            $query = $query->where('updated_at','<=',$last_activity_end_date);
                        }
                    })->with(['tasks' => function ($query) {
                        $query->with('leadstatus','users','projects');
                    }]);
                    //filter by input
                if($created_at_start_date)
                {
                    $created_at_start_date = $created_at_start_date.' 00:00:00';
                    $contact = $contact->where('created_at','>=',$created_at_start_date);
                }
                if($created_at_end_date)
                {
                    $created_at_end_date = $created_at_end_date.' 23:59:59';
                    $contact = $contact->where('created_at','<=',$created_at_end_date);
                }
                $contact = $contact->get();
                //
                }
           $roleUser = RoleUser::where('role_id','4')->pluck('user_id');
           $salesAgents = User::whereIn('id',$roleUser)->get(array("users.id as value","users.name as label"));
           $projects = Project::whereIn('branch_id',$branch_id)->where('status','active')->get(array("projects.id as value","projects.project_name as label"));
       }
        return response()->json([
        "success" => true,
        "message" => "Contact List",
        "data" => $contact,
        "salesAgent" => $salesAgents,
        "leadstatus" => $leadstatus,
        "automaticStatus" => $automaticStatus,
        'projects' => $projects,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = Auth::id();
        $userRole = RoleUser::where('user_id',$id)->first();
        $input = $request->all();
        $validator = Validator::make($input, [
        // 'email' => 'required',
        // 'first_name' => 'required',
        // 'last_name' => 'required',
        // 'contact_owner_id' => 'required',
        // 'job_title' => 'required',
        // 'company_name' => 'required',
        // 'mobile_phone_number' => 'required',
        // 'phone_number' => 'required',
        // 'street_address' => 'required',
        // 'lifecycle_stage_id' => 'required',
        // 'lead_status_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
//if sales agent create new contact
// get auth user branch
if($request->branch_id == null)
{
    //branch admin
    $branch = BranchUsers::where('user_id',$id)->first();
    $branch_id = $branch->branch_id;
    $input['branch_id'] = $branch_id;
}
    $branch_id = $input['branch_id'];
    // create project
    if($request->project_id == null)
    {
        $project = Project::create($input);
        $input['project_id'] = $project->id;
    }
    else
    {
        $input['project_id'] = $request->project_id;
    }
    //
    $input['created_by'] = $id;
    $input['updated_by'] = $id;
    $contact = Contact::create($input);
    // $contact = Contact::with('leadstatus')->find($contact->id);
    $input['contact_id'] = $contact->id;
    $input['lead_status_id'] = '1'; // unassigned (lead status)
    if($userRole->role_id =='4')
    {
        $input['assigned_to_id'] = $id;
        $input['assigned_by_id'] = $branch_id;
        $input['lead_status_id'] = '1'; // new (lead status)
    }
    $tasks = Task::create($input);
        return response()->json([
        "success" => true,
        "message" => "Contact created successfully.",
        "data" => $contact
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $contact = Contact::find($id)->with(['tasks' => function($q){
        //     $q->with('leadstatus','users','projects');
        // }])->get();
        // if (is_null($contact)) {
        // return $this->sendError('Contact not found.');
        // }
        // return response()->json([
        // "success" => true,
        // "message" => "Contact retrieved successfully.",
        // "data" => $contact
        // ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
            // 'email' => 'required',
            // 'first_name' => 'required',
            // 'last_name' => 'required',
            // 'contact_owner_id' => 'required',
            // 'job_title' => 'required',
            // 'company_name' => 'required',
            // 'mobile_phone_number' => 'required',
            // 'phone_number' => 'required',
            // 'street_address' => 'required',
            // 'lifecycle_stage_id' => 'required',
            // 'lead_status_id' => 'required', 
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $contact = Contact::find($id);
        // dd($contact->branch_id);
        $contact->email = $input['email'];
        $contact->first_name = $input['first_name'];
        $contact->last_name = $input['last_name'];
        $contact->contact_owner_id = $input['contact_owner_id'];
        $contact->job_title = $input['job_title'];
        $contact->company_name = $input['company_name'];
        $contact->mobile_phone_number = $input['mobile_phone_number'];
        $contact->phone_number = $input['phone_number'];
        $contact->street_address = $input['street_address'];
        $contact->lifecycle_stage_id = $input['lifecycle_stage_id'];
        $contact->lead_status_id = $input['lead_status_id'];
        $contact->updated_by = $user_id;
        $contact->save();
        // $input['project_id'] = $id;
        // $input['updated_by'] = $id;
        // $input['project_id'] = $id;
        // $input['updated_by'] = $id;

        return response()->json([
        "success" => true,
        "message" => "Contact updated successfully.",
        "data" => $contact
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contact = Contact::find($id);
        // dd($contact);
        $contact->delete();
        return response()->json([
        "success" => true,
        "message" => "Contact deleted successfully.",
        "data" => $contact
        ]);
    }

    
    public function revisionTask(Request $request, $id)
    {
        $filter = $request->filter;
        // $remainder = $request->remainder;
        $authUserId = Auth::id();
        $authUserRoleId = RoleUser::where('user_id',$authUserId)->first();
        if($authUserRoleId->role_id == 3) //Branch Admin
        {
            $contact = Contact::where('id',$id)->with(['revisionTask' => function($q) use($filter){
                if($filter)
                {
                    $q->where($filter, '!=', null);
                }
                $q->with('leadstatus','users','projects')->latest();
            }])->first();
        }
        elseif($authUserRoleId->role_id == 4) //Sales Agent
        {
            $contact = Contact::where('id',$id)->with(['revisionTask' => function($q) use($authUserId, $filter){
                if($filter)
                {
                    $q->where($filter, '!=', null);
                }
                $q->where('assigned_to_id',$authUserId)->with('leadstatus','users','projects')->latest();
            }])->first();
        }
        return response()->json([
            "success" => true,
            "message" => "Contact List",
            "data" => $contact,
            ]); 
    }

    public function automaticStatus(Request $request)
    {
        $user_id = Auth::id();
        $input = $request->all();
        $input['admin_id'] = $user_id;
        $tasks = AutomaticAssigning::where('admin_id',$user_id)->first();
        if($tasks == null)
        {
            $tasks = AutomaticAssigning::create($input);
        }
        else
        {
            $tasks['status'] = $request->status;
            $tasks->save();
        }
        return response()->json([
            "success" => true,
            "message" => "success",
            ]);
    }

    public function leadFromLandingPage(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
        // 'first_name' => 'required',
        // 'email' => 'required',
        // 'phone_number' => 'required',
        // 'landing_page_link' => 'required',
        // 'street_address' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        //no of repeats
        $no_of_repeats = Contact::where('email',$request->email)->count();
        // dd($no_of_repeats);
        $input['no_of_repeats'] = $no_of_repeats;
        $customer = Contact::create($input);
        $input['contact_id'] = $customer->id;
        $input['lead_status_id'] = '1';
        $tasks = Task::create($input);

        // find the admin for current branch
        $branchUsers = BranchUsers::where('branch_id',$request->branch_id)->pluck('user_id');
        $roleUsers = RoleUser::whereIn('user_id',$branchUsers)->where('role_id','3')->first();
        $userId = $roleUsers->user_id;



        //check whether autoassigning is active or inactive for this admin
        $autoAssign = AutomaticAssigning::where('admin_id',$userId)->first();
        if(!$autoAssign == null)
        {
            if($autoAssign->status == 'active')
            {
                //find all salesagent of current branch
                //branch_id = 2
                $branchUsers = BranchUsers::where('branch_id',$request->branch_id)->pluck('user_id');
                $roleUsers = RoleUser::whereIn('user_id',$branchUsers)->where('role_id','4')->pluck('user_id');
                $userTasks = User::whereIn('id',$roleUsers)->withCount('tasks')->orderBy('tasks_count', 'asc')->first();
                $input['branch_id'] = $request->branch_id;
                $input['contact_id'] = $customer->id;
                $input['lead_status_id'] = '1';
                $input['assigned_to_id'] = $userTasks->id;
                $input['created_by'] = $userId;
                $input['updated_by'] = $userId;
                $input['assigned_by_id'] = $userId;
               
                $task = Task::create($input);             
            }
        }
    

        $customer = Contact::find($customer->id);
        return response()->json([
        "success" => true,
        "message" => "Lead created successfully.",
        "data" => $customer
        ]);
    }
}
