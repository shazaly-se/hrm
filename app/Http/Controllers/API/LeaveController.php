<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Leavetype;
use App\Models\LeaveInfo;
use App\Models\EmployeeInfo;
use Illuminate\Http\Request;
Use \Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\Auth;
class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $date = Carbon::now();
        $todayDate= $date->toDateString(); 

        $user = auth()->user();
        $employee= EmployeeInfo::where("user_id",$user->id)->first();

       // return $employee;
        $leavetypes = Leavetype::all();
        $leaves = LeaveInfo::join('employeeinfos','employeeinfos.id',"leavesinfos.employee_id")
        ->join('leavetypes','leavetypes.id',"leavesinfos.leave_type")
        ->join('leavesstatus','leavesstatus.id',"leavesinfos.leave_status")
        ->where("employee_id",$employee->id)
         ->get(array("leavesinfos.*","employeeinfos.fullname","leavetypes.name as leavetype","leavesstatus.status"));

      //  $leave = Leave::with('employee','users')->where('branch_id',2)->get();
        // dd($leave);
        return response()->json([
        "success" => true,
        "message" => "Leave List",
        "leaves" => $leaves,
        "leavetypes"=>$leavetypes
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
        $user = auth()->user();
        $employee= EmployeeInfo::where("user_id",$user->id)->first();
        $to = \Carbon\Carbon::createFromFormat('Y-m-d', $request->start_date);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d', $request->end_date);
        $diff_in_days = $to->diffInDays($from);
      
        $leave= new LeaveInfo;
        $leave->employee_id=$employee->id;
        $leave->leave_type=$request->leave_type;
        $leave->leave_status=1;
        $leave->number_of_date=$diff_in_days;
        $leave->start_date=$request->start_date;
        $leave->end_date=$request->end_date;
        $leave->reason=$request->reason;
        $leave->save();

        return response()->json([
        "success" => true,
        "message" => "Leave created successfully.",
        "leave" => $leave
        ]);

        // $input = $request->all();
        // $validator = Validator::make($input, [
        // // 'employee_id' => 'required',
        // // 'application_date' => 'required',
        // // 'leave_from_date' => 'required',
        // // 'leave_to_date' => 'required',
        // // 'number_of_days' => 'required',
        // // 'reason' => 'required',
        // // 'status' => 'required',
        // // 'branch_id' => 'required',
        // ]);
        // if($validator->fails()){
        // return $this->sendError('Validation Error.', $validator->errors());       
        // }
        // $leave = Leave::create($input);
        // return response()->json([
        // "success" => true,
        // "message" => "Leave created successfully.",
        // "data" => $leave
        // ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $leave = LeaveInfo::join('employeeinfos','employeeinfos.id',"leavesinfos.employee_id")
        ->join('leavetypes','leavetypes.id',"leavesinfos.leave_type") 
        ->where("leavesinfos.id",$id)
        ->first(array("leavesinfos.*","employeeinfos.fullname","leavetypes.id as leavetypeid","leavetypes.name as leavetype"));
        // $leave = Leave::find($id);
        // if (is_null($leave)) {
        // return $this->sendError('Leave not found.');
        // }
        if(!$leave){
            return response()->json([
                "success" => false,
                "message" => "leave  ".$id."  does not exist",
               
                ]);
        }else{
            return response()->json($leave);
        }
      
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function edit(Leave $leave)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
       // return $request->all();
       $user = auth()->user();
       $employee= EmployeeInfo::where("user_id",$user->id)->first();
         $leave = LeaveInfo::find($id);
         if(!$leave){
            return response()->json([
                "success" => false,
                "message" => "leave  ".$id."  does not exist",
               
                ]);
        }
         $leave->employee_id=$employee->id;
         $leave->leave_type=$request->leave_type;
         $leave->number_of_date=$request->number_of_date;
         $leave->start_date=$request->start_date;
         $leave->end_date=$request->end_date;
         $leave->reason=$request->reason;
         $leave->update();
         return response()->json([
            "success" => true,
            "message" => "successfully updated",
            "data" => $leave
            ]);
    } 
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'employee_id' => 'required',
        'application_date' => 'required',
        'leave_from_date' => 'required',
        'leave_to_date' => 'required',
        'number_of_days' => 'required',
        'reason' => 'required',
        'status' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $leave->employee_id = $input['employee_id'];
        $leave->application_date = $input['application_date'];
        $leave->leave_from_date = $input['leave_from_date'];
        $leave->leave_to_date = $input['leave_to_date'];
        $leave->number_of_days = $input['number_of_days'];
        $leave->reason = $input['reason'];
        $leave->status = $input['status'];
        $leave->approved_datetime = $input['approved_datetime'];
        $leave->approved_by = $input['approved_by'];
        $leave->branch_id = $input['branch_id'];
        $leave->updated_by = $user_id;
        $leave->save();
        return response()->json([
        "success" => true,
        "message" => "Leave updated successfully.",
        "leave" => $leave
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $leave = LeaveInfo::find($id);
    
        if(!$leave){
            return response()->json([
                "success" => false,
                "message" => "leave ".$id." does not exist",
                ]);
              }
        $leave->delete();
        return response()->json([
        "success" => true,
        "leave" => $leave
        ]);
    }
}
