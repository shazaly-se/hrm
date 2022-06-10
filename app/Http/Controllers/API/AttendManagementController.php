<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use \Carbon\Carbon;
use App\Models\Attend;
use App\Models\EmployeeInfo;
use App\Models\CompanyInfo;
use App\Models\MonthlyDay;
use App\Models\LeaveInfo;
use Illuminate\Support\Facades\Auth;
use DateTime;
class AttendManagementController extends Controller
{

    public function allemployee(){
        $allemployees=EmployeeInfo::get(array("employeeinfos.id as value","employeeinfos.fullname as label"));
        return response()->json([
            "success" => true,
            "allemployees" => $allemployees,
            ]);
    }
    public function index(){
        // $to = \Carbon\Carbon::parse('18:00:00');
        // $from = \Carbon\Carbon::parse('10:30:00');
        // $diff_in_hours = $to->diffInMinutes($from);
        // return $diff_in_hours / 60;
        

        $attends=Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
        ->get(array("attends.*","employeeinfos.fullname","employeeinfos.image"));
        return response()->json([
            "success" => true,
            "attends" => $attends,
            ]);
    }

    public function search(Request $request)
    {
       
        $attends = Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
        ->where("fullname",'LIKE', "%".$request->searchedName."%")
            ->where(function ($query) use($request){
            if($request->start_date !=null  && $request->end_date!=null ) {$query->whereBetween("attends.created_at",[$request->start_date, $request->end_date]);}
             })
         ->get(array("attends.*","employeeinfos.fullname","employeeinfos.image"));
            return response()->json([
            "success" => true,
            "attends" => $attends,
            ]);
    }

    public function report(Request $request){
        // return $request->searchedName["value"];
    
        // return $attends;

        $company = CompanyInfo::first();
        

        $currentDate = new Carbon($request->start_date);
        
        $currentMonth =  $currentDate->month;
        $currentYear = $currentDate->year;
    

        $monthlydays = MonthlyDay::where("month_name",$currentMonth)->first();
        // return $monthlyhours->expected_hours;
        // return $currentMonth;
        // $expected = Carbon::now()->toDateString();
        // return $expected;
        // ->whereYear('date', Carbon::now()->month)

        $employeeInfo = EmployeeInfo::join("employee_details","employee_details.employee_id","=","employeeinfos.id")
        ->where("employeeinfos.id",$request->searchedName["value"])
        ->first(array("employeeinfos.fullname","total_salary"));

        $attends = Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
        ->where("employeeinfos.id",$request->searchedName["value"])
          ->where(function ($query) use($request){
          if($request->start_date !=null  && $request->end_date!=null ) {$query->whereBetween("attends.date",[$request->start_date, $request->end_date]);}
           })
           ->whereRaw('MONTH(date) = ?',[$currentMonth])     
        ->whereRaw('YEAR(date) = ?',[$currentYear]) 
       ->get(array("attends.*","employeeinfos.fullname","employeeinfos.image"));



       $regular_hours= Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
         ->where("employeeinfos.id",$request->searchedName["value"])
             ->where(function ($query) use($request){
             if($request->start_date !=null  && $request->end_date!=null ) {$query->whereBetween("attends.date",[$request->start_date, $request->end_date]);}
              })
              ->whereRaw('MONTH(date) = ?',[$currentMonth])     
        ->whereRaw('YEAR(date) = ?',[$currentYear]) 
         ->sum("regular_hours");

         $worked_hours= Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
         ->where("employeeinfos.id",$request->searchedName["value"])
             ->where(function ($query) use($request){
             if($request->start_date !=null  && $request->end_date!=null ) {$query->whereBetween("attends.date",[$request->start_date, $request->end_date]);}
              })
              ->whereRaw('MONTH(date) = ?',[$currentMonth])     
        ->whereRaw('YEAR(date) = ?',[$currentYear]) 
         ->sum("total_worked_hours");

         $check_in_late_number = Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
         ->where("employeeinfos.id",$request->searchedName["value"])
         ->where("check_in_late",1)
             ->where(function ($query) use($request){
             if($request->start_date !=null  && $request->end_date!=null ) {$query->whereBetween("attends.date",[$request->start_date, $request->end_date]);}
              })
              ->whereRaw('MONTH(date) = ?',[$currentMonth])     
        ->whereRaw('YEAR(date) = ?',[$currentYear]) 
         ->count("check_in_late");

         $break_time_late_number = Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
         ->where("employeeinfos.id",$request->searchedName["value"])
         ->where("resume_late",1)
             ->where(function ($query) use($request){
             if($request->start_date !=null  && $request->end_date!=null ) {$query->whereBetween("attends.date",[$request->start_date, $request->end_date]);}
              })
              ->whereRaw('MONTH(date) = ?',[$currentMonth])     
        ->whereRaw('YEAR(date) = ?',[$currentYear]) 
         ->count("resume_late");

         $worked_days = Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
         ->where("employeeinfos.id",$request->searchedName["value"])
        
             ->where(function ($query) use($request){
             if($request->start_date !=null  && $request->end_date!=null ) {$query->whereBetween("attends.date",[$request->start_date, $request->end_date]);}
              })
              ->whereRaw('MONTH(date) = ?',[$currentMonth])     
        ->whereRaw('YEAR(date) = ?',[$currentYear]) 
         ->count("attends.employee_id");

         $approved_leave = LeaveInfo::join("employeeinfos","employeeinfos.id","leavesinfos.employee_id")
         ->where("employeeinfos.id",$request->searchedName["value"])
         ->where("leave_status",2)
             ->where(function ($query) use($request){
             if($request->start_date !=null  && $request->end_date!=null ) {$query->whereBetween("leavesinfos.created_at",[$request->start_date, $request->end_date]);}
              })
              ->whereRaw('MONTH(date) = ?',[$currentMonth])     
        ->whereRaw('YEAR(date) = ?',[$currentYear]) 
         ->count("leavesinfos.employee_id");

         $absent_day = $monthlydays->expected_worked_days - ($approved_leave + $worked_days);



         $total_late= $check_in_late_number + $break_time_late_number;
         //return $total_late;
         $employee_salary_per_day = $employeeInfo->total_salary / $monthlydays->expected_worked_days;

         $expected_deduct = ($total_late * $company->amount) + ($absent_day * $employee_salary_per_day);
         $expected_deduct_days=  $absent_day * $company->amount_per_day;


         $salary_after_deduct = $employeeInfo->total_salary - ($expected_deduct);




         $mins= Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
         ->where("employeeinfos.id",$request->searchedName["value"])
             ->where(function ($query) use($request){
             if($request->start_date !=null  && $request->end_date!=null ) {$query->whereBetween("attends.date",[$request->start_date, $request->end_date]);}
              })
         ->sum("taken_time_in_min");

             
   
            return response()->json([

            "success" => true,
            "employeeInfo"=>$employeeInfo,
            "regular_hours"=>$regular_hours,
            "worked_hours"=>$worked_hours,
            "start_date"=>$request->start_date,
            "end_date"=>$request->end_date,
            "attends" => $attends,
            "check_in_late_number"=>$check_in_late_number,
            "break_time_late_number"=>$break_time_late_number,
            "total_late"=>$total_late,
            "total_salary"=>$employeeInfo->total_salary,
            "company_deduct_amount" =>$company->amount,
            "expected_deduct"=>$expected_deduct,
            "salary_after_deduct"=>$salary_after_deduct,
            "monthlydays"=>$monthlydays,
            "approved_leave"=>$approved_leave,
            "absent_day"=>$absent_day,
            "employee_salary_per_day"=>$employee_salary_per_day
            
            ]);
    }
}
