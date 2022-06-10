<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use \Carbon\Carbon;
use App\Models\Attend;
use App\Models\LeaveInfo;
use App\Models\EmployeeInfo;
use App\Models\CompanyInfo;
use App\Models\UserLocation;
use Illuminate\Support\Facades\Auth;
use DB;
class AttendController extends Controller
{
    public function recent(){
        $user = auth()->user();
       
        $employee= EmployeeInfo::where("user_id",$user->id)->first();
        
        $recent= Attend::where("employee_id",$employee->id)->take(3)
        ->select("attends.check_in","attends.check_out","attends.check_in","total_worked_hours",DB::raw('DAYNAME(date) as dayname'))->get();
        return response()->json([
            "success" => true,
            "recent" => $recent
            ]);

    }
    public function totalhours(){

        

        $user = auth()->user();
       
        $employee= EmployeeInfo::where("user_id",$user->id)->first();
        
        $hours= Attend::whereMonth('date', Carbon::now()->month)
        ->whereYear('date', Carbon::now()->year)
        ->where("employee_id",$employee->id)
        ->sum("total_worked_hours");

        $leave_days= LeaveInfo::whereYear('created_at', Carbon::now()->year)
        ->where("employee_id",$employee->id)
        ->where("leave_status",2)
        ->sum("number_of_date");
        $remaining= 30 - $leave_days;

           return response()->json([
            "success" => true,
            "total_hours" => round($hours,1),
            "leave_days" => $remaining
            ]);

    //     $total_hours= Attend::select(DB::raw('count(id) as `data`'), DB::raw("DATE_FORMAT(date, '%m-%Y') new_date"),  DB::raw('YEAR(date) year, MONTH(date) month'))
    //     ->groupby('date')
    //     ->where("employee_id",$employee->id)
    //    ->get();
    //    return $total_hours;

        // $attends=Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
        // ->get(array("attends.*","employeeinfos.fullname"));
        // return response()->json([
        //     "success" => true,
        //     "attends" => $attends,
        //     ]);


    }

    public function index(){
        $attends=Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
        ->get(array("attends.*","employeeinfos.fullname"));
        return response()->json([
            "success" => true,
            "attends" => $attends,
            ]);
    }

    public function attendexists()
    {
      
        $user = auth()->user();
       // return $user;

//         $timeFirst  = strtotime('2011-05-12 18:20:20');
// $timeSecond = strtotime('2011-05-13 18:20:20');
// $differenceInSeconds = $timeSecond - $timeFirst;
// return $differenceInSeconds;
       
        $employee= EmployeeInfo::where("user_id",$user->id)->first();
     //   return $employee;
        $date = Carbon::now();
        $todayDate= $date->toDateString(); 
       // return $todayDate;
        $time= $date->toTimeString();
        $emp_record= Attend::where("date",$todayDate)
        ->where("employee_id",$employee->id)
        ->first();
      //  return $emp_record;
       
      
      
        //return $different_time;
        if($emp_record)
        {
            $different_time= strtotime($time) - strtotime($emp_record->check_in);
            if($emp_record->check_status ==1)
            {  
                return response()->json([
                "record" => true,
                "check_status"=>"check_in",
                "checkOut"=>false,
                "different_time"=>$different_time
                ]);
            }else
            if($emp_record->check_status ==2){
                return response()->json([
                    "record" => true,
                    "check_status"=>"goBreak",
                    "checkOut"=>false,
                    "different_time"=>$different_time
                    ]);
            }else
            if($emp_record->check_status ==3){
                return response()->json([
                    "record" => true,
                    "check_status"=>"resume",
                    "checkOut"=>false,
                    "different_time"=>$different_time
                    ]);
            }else
            if($emp_record->check_status ==4){
                return response()->json([
                    "record" => true,
                    "check_status"=>"check_out",
                    "checkOut"=>true,
                    "different_time"=>$different_time
                    ]);
            }

            // if($emp_record->check_in !=null || $emp_record->check_in !="")
            // {  
            //     return response()->json([
            //     "record" => true,
            //     "check_in" => true
            //     ]);
            // }

            // if($emp_record->goBreak !=null || $emp_record->goBreak !="")
            // {  
            //     return response()->json([
            //     "record" => true,
            //     "goBreak" => true
            //     ]);
            // }

            // if($emp_record->resume !=null || $emp_record->resume !="")
            // {  
            //     return response()->json([
            //     "no_record" => true,
            //     "resume" => true
            //     ]);
            // }
       
        }else{
            return response()->json([
                "record" => false,
                "checkOut"=>false,
               "different_time"=>0
                ]);
        }
    }

    public function store(Request $request){
    //return $request->myLocation["longitude"];



    //if()
      $user = auth()->user();
      
     //return $user;
      $employee= EmployeeInfo::where("user_id",$user->id)->first();
     // return $employee;
      $company = CompanyInfo::join("employeeinfos","employeeinfos.company_id","companyinfos.id")
      ->where("employeeinfos.id",$employee->id)
      ->where("companyinfos.ip_address",$request->ip_address)
      ->first("companyinfos.*");
      //return $company;
      $date = Carbon::now();
     // return $date;

    $todayDate= $date->toDateString(); 
    $time= $date->toTimeString();
   //return $time ."-".$request->checkIn;

     

      if($company){

        $checkin_time = \Carbon\Carbon::parse($request->checkIn);
        //return $to;
        $company_time = \Carbon\Carbon::parse($company->last_time_allow);
  
          $test = $checkin_time->gt($company_time);
     

        $date = Carbon::now();
        //return $date;

      $todayDate= $date->toDateString(); 
      $time= $date->toTimeString();

     

   
     $new_attend_employee_record = new Attend;
     
  
      $emp_record= Attend::where("date",$todayDate)
      ->where("employee_id",$employee->id)
      ->first();

      
      
     
      if($emp_record)
      {
        
          // time management
              
          if($request->goBreak >0){
            $emp_record->goBreak=$time;
            $emp_record->check_status=2;
            $emp_record->update();
            return response()->json(["success"=>true,"meg"=>"successfully go break "]);
             }else
                    if($request->resume >0)
                    {
                     
                        
                        $allowedtime= $company->total_break_time * 60;
                        $total_allow_break = $allowedtime + $company->max_break_late_min;
                       // return $total_allow_break;
                        $go = \Carbon\Carbon::parse($emp_record->goBreak);// strtotime("00:00:00");
                 //return $go;

                        $back =  \Carbon\Carbon::parse($time); //strtotime("01:23:45");
                       // return "back at".$back;
                        $difference = $go->diffInMinutes($back);
                        //return $difference;

                  

                      
                      
                        $emp_record->resume=$time;
                        if($difference > $total_allow_break){
                            
                            //if()
                            $emp_record->resume_late=1;
                        }else{
                            $emp_record->resume_late=0;
                        }
                        $emp_record->check_status=3;
                        $emp_record->update();
                        return response()->json(["success"=>true,"meg"=>"successfully update resume "]);
                    }else
                    if($request->checkOut >0){

                        $to = \Carbon\Carbon::parse($time);
                        $date1 = strtotime($emp_record->check_in);
                        $date2 = strtotime($to);
                    $diff = abs($date2 - $date1);
                    $years = floor($diff / (365*60*60*24));
                    $months = floor(($diff - $years * 365*60*60*24)
                                                   / (30*60*60*24));
                   
                    // To get the day, subtract it with years and
                    // months and divide the resultant date into
                    // total seconds in a days (60*60*24)
                    $days = floor(($diff - $years * 365*60*60*24 -
                                 $months*30*60*60*24)/ (60*60*24));
                   
                    // To get the hour, subtract it with years,
                    // months & seconds and divide the resultant
                    // date into total seconds in a hours (60*60)
                    $hours = floor(($diff - $years * 365*60*60*24
                           - $months*30*60*60*24 - $days*60*60*24)
                                                       / (60*60));
                   
                    // To get the minutes, subtract it with years,
                    // months, seconds and hours and divide the
                    // resultant date into total seconds i.e. 60
                    $minutes = floor(($diff - $years * 365*60*60*24
                             - $months*30*60*60*24 - $days*60*60*24
                                              - $hours*60*60)/ 60);
                   
                    // To get the minutes, subtract it with years,
                    // months, seconds, hours and minutes
                    $seconds = floor(($diff - $years * 365*60*60*24
                             - $months*30*60*60*24 - $days*60*60*24
                                    - $hours*60*60 - $minutes*60));
                   
                    // Print the result
                   
                     // return "check out   ".$hours.":". $minutes;


                        $to = \Carbon\Carbon::parse($time);
                        $from = \Carbon\Carbon::parse($emp_record->check_in);
                        $diff_in_min = $to->diffInMinutes($from);
                        $diff_in_hours = $diff_in_min / 60;
                        $taken_time_in_hours= $emp_record->taken_time_in_min / 60;
                        $emp_record->check_out=$time;
                        $emp_record->total_worked_hours=$hours;
                        $emp_record->worked_minute=$minutes;
                        $emp_record->check_status=4;
                        $emp_record->update();
                          return response()->json(["success"=>true,"meg"=>"successfully check out"]);
                      //  return $taken_time_in_hours;

                        // if($emp_record->taken_time_in_min > 60){
                        //     $total_worked_hours = $diff_in_hours - $taken_time_in_hours;
                        //     $total_day_salary = 16.66666666666666 * $total_worked_hours;

                        //     //return "total_worked_hours   ".round($total_worked_hours,1) ."\n  total_day_salary  ".round($total_day_salary,1);

                        //     $emp_record->total_worked_hours = $total_worked_hours;
                        //     $emp_record->show_worked_hours = $total_worked_hours;
                        //     $emp_record->day_salary = $total_day_salary;
                        //     $emp_record->show_day_salary = $total_day_salary;
                        //     $emp_record->check_out=$time;
                        //     $emp_record->update();
                        //     return response()->json(["success"=>true,"meg"=>"successfully "]);
                        //     //return "diff_in_hours   ".$diff_in_hours ."\n    total_worked_hours   ".$total_worked_hours."\n    taken_time_in_min   ".$emp_record->taken_time_in_min;

                        // }else{
                        //     $total_worked_hours = $diff_in_hours - 1;
                        //     $total_day_salary= 16.66666666666666 * $total_worked_hours;
                        //   //  return "total_worked_hours   ".round($total_worked_hours,1) ."\n  total_day_salary  ".round($total_day_salary,1);
                        //     $emp_record->total_worked_hours = $total_worked_hours;
                        //     $emp_record->show_worked_hours = $total_worked_hours;
                        //     $emp_record->day_salary = $total_day_salary;
                        //     $emp_record->show_day_salary = $total_day_salary;
                        //     $emp_record->check_out=$time;
                        //     $emp_record->update();
                        //     return response()->json(["success"=>true,"meg"=>"successfully "]);
                        //   //  return "total_hours   ".$diff_in_hours ."\n    taken_time_in_hours   ".$taken_time_in_hours."\n    taken_time_in_min   ".$emp_record->taken_time_in_min;
                        // }
                
                       // return "dif hour  ".$diff_in_hours ;

                        // $emp_record->check_out=$time;
                        // $emp_record->update();
                        return response()->json(["success"=>true,"meg"=>"successfully "]);
                    }

     }
    else
     {

        if($request->checkIn > 0)
        {
             $lasttime= strtotime($company->last_time_allow) ;
           //  return $lasttime;
             $mytime = strtotime($time);
              //return $mytime;
     
            $company_starttime = \Carbon\Carbon::parse($company->start_time);
            $company_endtime = \Carbon\Carbon::parse($company->check_out_time);
            $regular_hours = $company_starttime->diffInHours($company_endtime) - $company->total_break_time;
               

           
            $new_attend_employee_record->employee_id=$employee->id;
            $new_attend_employee_record->date= $date;
            $new_attend_employee_record->check_in=$time;
            if($mytime > $lasttime){
                $new_attend_employee_record->check_in_late=1;
            }else{
                $new_attend_employee_record->check_in_late=0;
            }
            $new_attend_employee_record->goBreak="00:00:00";
            $new_attend_employee_record->resume="00:00:00";
            $new_attend_employee_record->check_out="00:00:00";
            $new_attend_employee_record->regular_hours=$regular_hours;
            $new_attend_employee_record->total_worked_hours=0;
            $new_attend_employee_record->worked_minute=0;
            $new_attend_employee_record->check_status=1;
            

          //  $new_attend_employee_record->deduct_status=$test?1:0;
           // $new_attend_employee_record->total_deduct=$test?$company->amount:0;

            
            if($new_attend_employee_record->save()){
                
                $userlocation = new UserLocation;
                $userlocation->employee_id= $employee->id;
                $userlocation->date= $date;
                $userlocation->lat=$request->myLocation["latitude"];
                $userlocation->lng=$request->myLocation["longitude"];
                $userlocation->save();
            

            return response()->json(["success"=>true,"meg"=>"successfully checkin"]);
            }

        }else
        if($request->goBreak > 0)
        {
           
            $new_attend_employee_record->employee_id=$employee->id;
            $new_attend_employee_record->date=$date;
            $new_attend_employee_record->check_in="00:00:00";
            $new_attend_employee_record->goBreak=$time;
            $new_attend_employee_record->resume="00:00:00";
            
            $new_attend_employee_record->check_out="00:00:00";
            $new_attend_employee_record->save();
            return response()->json(["success"=>true,"meg"=>"successfully go break"]);
        }else
        if($request->resume >0){
            $new_attend_employee_record->employee_id=$employee->id;
            $new_attend_employee_record->date=$date;
            $new_attend_employee_record->check_in="00:00:00";
            $new_attend_employee_record->goBreak="00:00:00";
            $new_attend_employee_record->resume=$time;
            $new_attend_employee_record->check_out="00:00:00";
            $new_attend_employee_record->save();
            return response()->json(["success"=>true,"meg"=>"successfully back"]);
        }
        else
        if($request->checkOut >0){
            $new_attend_employee_record->employee_id=$employee->id;
            $new_attend_employee_record->date=$date;
            $new_attend_employee_record->check_in="00:00:00";
            $new_attend_employee_record->goBreak="00:00:00";
            $new_attend_employee_record->resume="00:00:00";
            $new_attend_employee_record->check_out=$time;
            $new_attend_employee_record->save();
            return response()->json(["success"=>true,"meg"=>"successfully checkout"]);
        }
        
    
    }
    return response()->json(["success"=>true,"meg"=>"successfully "]);
}else{
    return response()->json(["success"=>false,"meg"=>"You are out of range"]);

}
     
        //return $request->all();
    }

    public function all_attends_user(Request $request){
    
        $user = auth()->user();
     
        $employee= EmployeeInfo::where("user_id",$user->id)->first();
        
       
        //return $employee->id;

        if($request->dateRange==1)
        {
            
            $attend_record= Attend::where("employee_id",$employee->id)
            ->whereMonth("date",Carbon::now()->month)
            ->get(array("attends.id as key","date","check_in","goBreak","resume","check_out","total_worked_hours"));

            $total_worked_hours= Attend::where("employee_id",$employee->id)
            ->whereMonth("date",Carbon::now()->month)
            ->select(DB::raw('SUM(total_worked_hours) as total_worked_hours'))->get();
            return response()->json(["total_worked_hours"=>$total_worked_hours,"attend_record"=>$attend_record]);  
        }
        else
        if($request->dateRange==2)
        {
            $attend_record= Attend::where("employee_id",$employee->id)
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
           // ->where("date",Carbon::now()->year)
            ->get(array("attends.id as key","date","check_in","goBreak","resume","check_out","total_worked_hours"));
            $total_worked_hours= Attend::where("employee_id",$employee->id)
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->select(DB::raw('SUM(total_worked_hours) as total_worked_hours'))->get();
            return response()->json(["total_worked_hours"=>$total_worked_hours,"attend_record"=>$attend_record]); 
        }
        else
        if($request->dateRange==3)
        {
            $attend_record= Attend::where("employee_id",$employee->id)
            ->whereYear("date",Carbon::now()->year)
            ->get(array("attends.id as key","date","check_in","goBreak","resume","check_out","total_worked_hours"));
            $total_worked_hours= Attend::where("employee_id",$employee->id)
            ->whereYear("date",Carbon::now()->year)
            ->select(DB::raw('SUM(total_worked_hours) as total_worked_hours'))->get();
            return response()->json(["total_worked_hours"=>$total_worked_hours,"attend_record"=>$attend_record]); 
        }
        else
        {
            $attend_record= Attend::where("employee_id",$employee->id)
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
           // ->where("date",Carbon::now()->year)
            ->get(array("attends.id as key","date","check_in","goBreak","resume","check_out","total_worked_hours"));
            $total_worked_hours= Attend::where("employee_id",$employee->id)
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->select(DB::raw('SUM(total_worked_hours) as total_worked_hours'))->get();
            return response()->json(["total_worked_hours"=>$total_worked_hours,"attend_record"=>$attend_record]); 
        }
    

         
    }
}
