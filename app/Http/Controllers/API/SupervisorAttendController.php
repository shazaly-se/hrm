<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
Use \Carbon\Carbon;
use App\Models\Attend;
use App\Models\EmployeeInfo;
use App\Models\CompanyInfo;
use Illuminate\Http\Request;
use DB;

class SupervisorAttendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $department = EmployeeInfo::where("user_id",$user->id)->first(array("departmentselected"));
       // return $department;
        $role = DB::table("model_has_roles")
        ->join("roles","roles.id","model_has_roles.role_id")
        ->where("model_id",$user->id)->first(array("roles.*"));
        if($role->name =="supervisor"){
            $labours =  EmployeeInfo::where("departmentselected",$department->departmentselected)->get(array("fullname","id"));

            return response()->json(["success"=>true,"labours"=>$labours]); 

        }
       
    }

    public function labours(){

       

        $date = Carbon::now();
       $todayDate= $date->toDateString(); 
       $time= $date->toTimeString();

       $new_employees = EmployeeInfo::where("last_attend_status",0)
                                    ->get(array("id","fullname","image"));
       return response()->json(["success"=>true,"labours"=>$new_employees]); 
    }

    
    public function checked_in(){

       

        $date = Carbon::now();
        // return $date;
   
       $todayDate= $date->toDateString(); 
       $time= $date->toTimeString();
   

       $checked_in = EmployeeInfo::join("attends","attends.employee_id","employeeinfos.id")
       ->where("last_attend_status",1)
       ->where("date",$todayDate)
       ->get(array("employeeinfos.id","fullname","image"));



  
       return response()->json(["success"=>true,"checked_in"=>$checked_in]); 
    }

    
    public function go_break(){

       

        $date = Carbon::now();
        // return $date;
   
       $todayDate= $date->toDateString(); 
       $time= $date->toTimeString();
  

       $go_break = EmployeeInfo::join("attends","attends.employee_id","employeeinfos.id")
       ->where("last_attend_status",2)
       ->where("date",$todayDate)
       ->get(array("employeeinfos.id","fullname","image"));


    //    $check_out = EmployeeInfo::join("attends","attends.employee_id","employeeinfos.id")
    //    ->where("last_attend_status",4)
    //    ->where("date",$todayDate)
    //    ->get(array("employeeinfos.id","fullname","image"));

  
       return response()->json(["success"=>true,"go_break"=>$go_break]); 
    }
    
    
    public function resume(){

       

        $date = Carbon::now();
        // return $date;
   
       $todayDate= $date->toDateString(); 
       $time= $date->toTimeString();
   

       $resume = EmployeeInfo::join("attends","attends.employee_id","employeeinfos.id")
       ->where("last_attend_status",3)
       ->where("date",$todayDate)
       ->get(array("employeeinfos.id","fullname","image"));

    //    $check_out = EmployeeInfo::join("attends","attends.employee_id","employeeinfos.id")
    //    ->where("last_attend_status",4)
    //    ->where("date",$todayDate)
    //    ->get(array("employeeinfos.id","fullname","image"));

  
       return response()->json(["success"=>true,"resume"=>$resume]); 
    }

    
    public function checked_out_today(){

       

        $date = Carbon::now();
        // return $date;
   
       $todayDate= $date->toDateString(); 
       $time= $date->toTimeString();
    

       $checked_out = EmployeeInfo::join("attends","attends.employee_id","employeeinfos.id")
       ->where("last_attend_status",4)
       ->where("date",$todayDate)
       ->get(array("employeeinfos.id","fullname","image"));

  
       return response()->json(["success"=>true,"checked_out_today"=>$checked_out]); 
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
        $date = Carbon::now();
        //return $date;
      $todayDate= $date->toDateString(); 
      $time= $date->toTimeString();


      $employee= EmployeeInfo::where("id",$request->employee_id)->first();
     // return $employee;
       $company = CompanyInfo::join("employeeinfos","employeeinfos.company_id","companyinfos.id")
       ->where("employeeinfos.id",$employee->id)
       ->first("companyinfos.*");

    //    return $company;

    $new_attend_employee_record = new Attend;

    $emp_record= Attend::where("date",$todayDate)
    ->where("employee_id",$employee->id)
    ->first();

    
    if($emp_record)
    {
        if($request->goBreak >0){
            $emp_record->goBreak=$time;
            $emp_record->check_status=2;
            if($emp_record->update()){
                $employee->last_attend_status=2;
                $employee->update();
            return response()->json(["success"=>true,"meg"=>"successfully go break "]);
            }
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
                 if($emp_record->update()){
                    $employee->last_attend_status=3;
                    $employee->update();
                 return response()->json(["success"=>true,"meg"=>"successfully update resume "]);
                 }
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
                 if($emp_record->update()){
                    $employee->last_attend_status=4;
                    $employee->update();
                   return response()->json(["success"=>true,"meg"=>"successfully check out"]);
                 }
             
                 
             }


    }else{

    
        if($request->checkIn > 0)
        {
             $lasttime= strtotime($company->last_time_allow) ;
            //return $lasttime;
             $mytime = strtotime($time);
              //return $mytime;
     
            $company_starttime = \Carbon\Carbon::parse($company->start_time);
            $company_endtime = \Carbon\Carbon::parse($company->check_out_time);
            $regular_hours = $company_starttime->diffInHours($company_endtime) - $company->total_break_time;
               

           
            $new_attend_employee_record->employee_id=$employee->id;
            $new_attend_employee_record->date=$date;
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
                $employee->last_attend_status=1;
                $employee->update();
                return response()->json(["success"=>true,"meg"=>"successfully checkin"]);
            }
           

        }
    }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
