<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\BranchUsers;
use App\Models\Company;
use App\Models\Task;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $projects = '';
        $id = Auth::id();
        // dd($id);
        // checking whether it is superadmin
       $companyId = Company::where('user_id',$id)->first();
    //    dd($companyId);
       if($companyId == NULL)
       {
            //checking whether it is sales agent
            $tasks = Task::where('assigned_to_id',$id)->pluck('contact_id');
            if($tasks->isEmpty())
            {
                // Branch Admin
                $branch_id = BranchUsers::where('user_id',$id)->first();
                $projects = Project::where('branch_id',$branch_id->branch_id)->get();
            }
            else
            {
                //Sales Agent
                $branch_id = BranchUsers::where('user_id',$id)->first();
                // $projectId = Task::where([
                //     ['branch_id', '==', $branch_id->branch_id],
                //     ['assigned_to_id', '==', $id] 
                // ])->get(); 
                $projectId = Task::where('assigned_to_id',$id)->pluck('project_id');
                $projects = Project::whereIn('id',$projectId)->get();
            }
       }
       else
       {
           // Company Super Admin
           $branch_id = Branch::where('company_id',$companyId->id)->pluck('id');
           $projects = Project::whereIn('branch_id',$branch_id)->get();
       }
        return response()->json([
        "success" => true,
        "message" => "Projects List",
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
         $input = $request->all();
         $validator = Validator::make($input, [
         // 'project_name' => 'required',
         // 'description' => 'required',
         // 'type' => 'required',
         ]);
         if($validator->fails()){
         return $this->sendError('Validation Error.', $validator->errors());       
         }
 
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
        $input['created_by'] = $id;
        $input['updated_by'] = $id;
        $project = Project::create($input);
   
         return response()->json([
         "success" => true,
         "message" => "Project created successfully.",
         "data" => $project
         ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return response()->json([
            "success" => true,
            "message" => "Project fetch by id successfully.",
            "data" => $project
            ]); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
            // 'project_name' => 'required',
            // 'description' => 'required',
            // 'type' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $project = Project::find($id);
        $project->project_name = $input['project_name'];
        $project->description = $input['description'];
        $project->type = $input['type'];      
        $project->updated_by = $user_id;
        $project->save();
        return response()->json([
        "success" => true,
        "message" => "Project updated successfully.",
        "data" => $project
        ]);
    }
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
            // 'type' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $project = Project::find($id);
        $project->type = $input['type'];      
        $project->updated_by = $user_id;
        $project->save();
        return response()->json([
        "success" => true,
        "message" => "Project status updated successfully.",
        "data" => $project
        ]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        //
    }
}
