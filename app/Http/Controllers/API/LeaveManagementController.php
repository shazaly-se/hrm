<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Leavetype;
use App\Models\LeaveInfo;
use App\Models\LeaveStatus;
use App\Models\EmployeeInfo;
use Illuminate\Http\Request;
Use \Carbon\Carbon;

use DB;
use Validator;
use Illuminate\Support\Facades\Auth;

class LeaveManagementController extends Controller
{
    public function index()
    {
        $date = Carbon::now();
        $todayDate= $date->toDateString(); 

        $leavetypes = Leavetype::all();
        $employees = EmployeeInfo::get(array("id","fullname"));

        $leavestatus = LeaveStatus::all();
        $leaves = LeaveInfo::join('employeeinfos','employeeinfos.id',"leavesinfos.employee_id")
        ->join('leavetypes','leavetypes.id',"leavesinfos.leave_type")
        ->join('leavesstatus','leavesstatus.id',"leavesinfos.leave_status")
        ->orderBy("leavesinfos.id","desc")
         ->get(array("leavesinfos.*","employeeinfos.fullname","employeeinfos.image","leavetypes.name as leavetype","leavesstatus.status"));

         $pending = DB::table('leavesinfos')
                ->select(DB::raw('count(leave_status) as  status_count'))
                ->where('leave_status',1)
                ->get();

                $today = DB::table('leavesinfos')
                ->select(DB::raw('count(leave_status) as  status_count'))
               // ->groupByRaw('leave_status')
                ->where('created_at',$todayDate)
                ->get();

                $all = DB::table('leavesinfos')
                ->select(DB::raw('count(leave_status) as  status_count'))
               // ->groupByRaw('leave_status')
                //->where('leave_status',1)
                ->get();

                $planed = DB::table('leavesinfos')
                ->select(DB::raw('count(leave_type) as  leave_count'))  
                ->where('leave_type',1)   
                ->get();

                $unplaned = DB::table('leavesinfos')
                ->select(DB::raw('count(leave_type) as  leave_count'))
                ->where('leave_type',"!=",1) 
                ->get();

      //  $leave = Leave::with('employee','users')->where('branch_id',2)->get();
        // dd($leave);
        return response()->json([
        "success" => true,
        "message" => "Leave List",
        "leaves" => $leaves,
        "employees"=>$employees,
        "leavestatus"=>$leavestatus,
        "leavetypes"=>$leavetypes,
        "pending"=>$pending,
        "today"=>$today,
        "all"=>$all,
        "planed"=>$planed,
        "unplaned"=>$unplaned
        ]);
    }

    public function postleave(Request $request){
        $date = Carbon::now();
        //return $date;

      $todayDate= $date->toDateString(); 
        $leave=new LeaveInfo;
        $leave->employee_id= $request->employee_id;
        $leave->date= $todayDate;
        $leave->leave_type= $request->leave_type;
        $leave->leave_status= 1;
        $leave->start_date= $request->start_date;
        $leave->end_date= $request->end_date;
        $leave->reason= $request->reason;
        if($leave->save()){
            $leaves = LeaveInfo::join('employeeinfos','employeeinfos.id',"leavesinfos.employee_id")
            ->join('leavetypes','leavetypes.id',"leavesinfos.leave_type")
            ->join('leavesstatus','leavesstatus.id',"leavesinfos.leave_status")
            ->orderBy("leavesinfos.id","desc")
             ->get(array("leavesinfos.*","employeeinfos.fullname","employeeinfos.image","leavetypes.name as leavetype","leavesstatus.status"));
    
             $pending = DB::table('leavesinfos')
                    ->select(DB::raw('count(leave_status) as  status_count'))
                    ->where('leave_status',1)
                    ->get();
    
                    $today = DB::table('leavesinfos')
                    ->select(DB::raw('count(leave_status) as  status_count'))
                   // ->groupByRaw('leave_status')
                    ->where('created_at',$todayDate)
                    ->get();
    
                    $all = DB::table('leavesinfos')
                    ->select(DB::raw('count(leave_status) as  status_count'))
                   // ->groupByRaw('leave_status')
                    //->where('leave_status',1)
                    ->get();
    
                    $planed = DB::table('leavesinfos')
                    ->select(DB::raw('count(leave_type) as  leave_count'))  
                    ->where('leave_type',1)   
                    ->get();
    
                    $unplaned = DB::table('leavesinfos')
                    ->select(DB::raw('count(leave_type) as  leave_count'))
                    ->where('leave_type',"!=",1) 
                    ->get();
    
          //  $leave = Leave::with('employee','users')->where('branch_id',2)->get();
            // dd($leave);
            return response()->json([
            "success" => true,
            "message" => "Leave List",
            "leaves" => $leaves,
            "pending"=>$pending,
            "today"=>$today,
            "all"=>$all,
            "planed"=>$planed,
            "unplaned"=>$unplaned
            ]);
        }
    }

    public function editleave($id){
        $leave = LeaveInfo::join('employeeinfos','employeeinfos.id',"leavesinfos.employee_id")
        ->join('leavetypes','leavetypes.id',"leavesinfos.leave_type")
        ->join('leavesstatus','leavesstatus.id',"leavesinfos.leave_status")
        ->where("leavesinfos.id",$id)
        ->orderBy("leavesinfos.id","desc")
         ->first(array("leavesinfos.*","employeeinfos.fullname","employeeinfos.image","leavetypes.name as leavetype","leavesstatus.status"));
         return response()->json($leave);
    }
    public function search(Request $request)
    {
       
        $leaves = LeaveInfo::join('employeeinfos','employeeinfos.id',"leavesinfos.employee_id")
        ->join('leavetypes','leavetypes.id',"leavesinfos.leave_type")
        ->join('leavesstatus','leavesstatus.id',"leavesinfos.leave_status")
        ->where("fullname",'LIKE', "%".$request->searchedName."%")
            ->where(function ($query) use($request){
            if($request->selected_leave_type > 0) {$query->where("leave_type",$request->selected_leave_type);}
            if($request->selected_leave_status > 0) {$query->where("leave_status",$request->selected_leave_status);}
            if($request->start_date !=null  && $request->end_date!=null ) {$query->whereBetween("leavesinfos.created_at",[$request->start_date, $request->end_date]);}

             })
             ->orderBy("leavesinfos.id","desc")
       
         ->get(array("leavesinfos.*","employeeinfos.fullname","employeeinfos.image","leavetypes.name as leavetype","leavesstatus.status"));

      //  $leave = Leave::with('employee','users')->where('branch_id',2)->get();
        // dd($leave);
        return response()->json([
        "success" => true,
        "message" => "Leave List",
        "leaves" => $leaves
        ]);
    }


    public function store(Request $request){
        $date = Carbon::now();
        $todayDate= $date->toDateString(); 

        $leave=LeaveInfo::where("id",$request->id)->first();
       // return $leave;
       

        if($request->status ==1){
         $leave->leave_status = 1;
         $leave->update();
        }else
        if($request->status ==2){
            $leave->leave_status= 2;
            $leave->update();
        }else
        if($request->status ==3){
            $leave->leave_status= 3;
            $leave->update();
        }

        $leaves = LeaveInfo::join('employeeinfos','employeeinfos.id',"leavesinfos.employee_id")
        ->join('leavetypes','leavetypes.id',"leavesinfos.leave_type")
        ->join('leavesstatus','leavesstatus.id',"leavesinfos.leave_status")
        ->orderBy("leavesinfos.id","desc")
         ->get(array("leavesinfos.*","employeeinfos.fullname","employeeinfos.image","leavetypes.name as leavetype","leavesstatus.status"));

         $pending = DB::table('leavesinfos')
                ->select(DB::raw('count(leave_status) as  status_count'))
                ->where('leave_status',1)
                ->get();

                $today = DB::table('leavesinfos')
                ->select(DB::raw('count(leave_status) as  status_count'))
               // ->groupByRaw('leave_status')
                ->where('created_at',$todayDate)
                ->get();

                $all = DB::table('leavesinfos')
                ->select(DB::raw('count(leave_status) as  status_count'))
               // ->groupByRaw('leave_status')
                //->where('leave_status',1)
                ->get();

                $planed = DB::table('leavesinfos')
                ->select(DB::raw('count(leave_type) as  leave_count'))  
                ->where('leave_type',1)   
                ->get();

                $unplaned = DB::table('leavesinfos')
                ->select(DB::raw('count(leave_type) as  leave_count'))
                ->where('leave_type',"!=",1) 
                ->get();

      //  $leave = Leave::with('employee','users')->where('branch_id',2)->get();
        // dd($leave);
        return response()->json([
        "success" => true,
        "message" => "Leave List",
        "leaves" => $leaves,
        "pending"=>$pending,
        "today"=>$today,
        "all"=>$all,
        "planed"=>$planed,
        "unplaned"=>$unplaned
        ]);
    }
}
