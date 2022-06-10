<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Company;
use App\Models\BranchUsers;
use App\Models\Contact;
use App\Models\Project;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::all();
        return response()->json([
        "success" => true,
        "message" => "Task List",
        "data" => $tasks
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
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
        $input = $request->all();
        $validator = Validator::make($input, [
        // 'assigned_by_id' => 'required',
        // 'assigned_to_id' => 'required',
        // 'contact_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        



        $companyId = Company::where('user_id',$id)->first();
        if($companyId == NULL)
        {
            $branch = BranchUsers::where('user_id',$id)->first();
            $branch_id = $branch->branch_id;
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
            $contacts = $request->contact_id;
            $integerIDs = array_map('intval', $contacts);
            foreach ($integerIDs as $contact)
            {
                // $input['assigned_to_id'] = $request->assigned_to_id;
                $input['contact_id'] = $contact;
                $input['assigned_by_id'] = $id;
                $input['created_by'] = $id;
                $input['updated_by'] = $id;
                $input['branch_id'] = $branch_id;
                $input['lead_status_id'] = '1';
                $input['comments'] = $request->comments;
                $task = Task::create($input);
            }
        }

        return response()->json([
        "success" => true,
        "message" => "Task created successfully.",
        "data" => $task
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $task = Task::find($id);
        if (is_null($task)) {
        return $this->sendError('Task not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Task retrieved successfully.",
        "data" => $task
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        dd($request->contacted_datetime);
        // dd($request->log_email);
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        // 'assigned_by_id' => 'required',
        // 'assigned_to_id' => 'required',
        // 'customer_id' => 'required',
        // 'lead_status_id'
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $task = Task::find($id);
        $input['branch_id'] = $task->branch_id;
        $input['contact_id'] = $task->contact_id;
        $input['assigned_to_id'] = $task->assigned_to_id;
        $input['assigned_by_id'] = $task->assigned_by_id;
        $input['project_id'] = $task->project_id;
        $input['lead_status_id'] = $task->lead_status_id;
        $input['comments'] = $request->comments;
        
        $input['created_by'] = $user_id;
        $input['updated_by'] = $user_id;
        if($request->notes)
        {
            $input['notes'] = $request->notes;
        }
        if($request->lead_status_id)
        {
            $input['lead_status_id'] = $request->lead_status_id;
        }
        if($request->remainder)
        {
            $input['remainder'] = $request->remainder;
        }
        if($request->email_subject)
        {
            $input['email_subject'] = $request->email_subject;
        }
        if($request->email_content)
        {
            $input['email_content'] = $request->email_content;
        }
        if($request->log_email)
        {
            $input['log_email'] = $request->log_email; 
        }
        if($request->email_description)
        {
            $input['email_description'] = $request->email_description;
        }
        if($request->contacted_datetime)
        {
            $input['contacted_datetime'] = $request->contacted_datetime; 
        }
        if($request->call_description)
        {
            $input['call_description'] = $request->call_description; 
        }
        if($request->call_outcome)
        {
            $input['call_outcome'] = $request->call_outcome; 
        }
        if($request->task_title)
        {
            $input['task_title'] = $request->task_title;
        }
        if($request->due_datetime)
        {
            $input['due_datetime'] = $request->due_datetime;
        }
        if($request->type)
        {
            $input['type'] = $request->type;
        }
        if($request->queue)
        {
            $input['queue'] = $request->queue;
        }
        if($request->task_note)
        {
            $input['task_note'] = $request->task_note;
        }
        $tasks = Task::create($input);
        //send notification here if remainder have time
        // if($request->remainder)
        // {
        //     return response()->json([
        //         "success" => true,
        //         "message" => "successfully send remainder notification.",
        //         "data" => $tasks
        //         ]);
        // }
        return response()->json([
        "success" => true,
        "message" => "successfully done.",
        "data" => $tasks
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $task = Task::find($request->id);
        $task->delete();
        return response()->json([
        "success" => true,
        "message" => "Task deleted successfully.",
        "data" => $task
        ]);
    }
    
}
