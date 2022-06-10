<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use \Carbon\Carbon;
use App\Models\Attend;
use App\Models\EmployeeInfo;
use App\Models\CompanyInfo;
use App\Models\MonthlyDay;
use App\Models\LeaveInfo;
use App\Models\PayRoll;
use App\Models\PayRollDetail;
use DB;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function employees()
    {
        $employee = EmployeeInfo::leftJoin("designations","designations.id","=","employeeinfos.designationselected")
        ->get(array("employeeinfos.*","designations.designationName"));
       
        return response()->json([
           "success" => true,
           "employees" => $employee
           ]);
    }

    public function getemployee(Request $request){

        $company = CompanyInfo::first();

        
        

        $currentDate = new Carbon($request->payrollmonth);
        
        $currentMonth =  $currentDate->month;
        $currentYear = $currentDate->year;

        //return $currentYear;

      
        
    

        $monthlydays = MonthlyDay::where("month_name",$currentMonth)->first();
      

        
        
        $employee = EmployeeInfo::join("employee_details","employee_details.employee_id","=","employeeinfos.id")
        ->where("employeeinfos.id",$request->employee_id)
        ->first(array("employee_details.employee_id","employee_details.total_salary"));

      

        //$payroldetails = PayRollDetail::where("employee_id",$request->employee_id)->first();


        $regular_hours= Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
        ->where("employeeinfos.id",$request->employee_id) 
        ->whereRaw('MONTH(date) = ?',[$currentMonth])     
        ->whereRaw('YEAR(date) = ?',[$currentYear])      
        ->sum("regular_hours");

        $worked_hours= Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
        ->where("employeeinfos.id",$request->employee_id)
        ->whereRaw('MONTH(date) = ?',[$currentMonth])  
        ->whereRaw('YEAR(date) = ?',[$currentYear]) 
        ->sum("total_worked_hours");

        

    

       $check_in_late_number = Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
       ->where("employeeinfos.id",$request->employee_id)
       ->whereRaw('MONTH(date) = ?',[$currentMonth])  
       ->whereRaw('YEAR(date) = ?',[$currentYear]) 
       ->where("check_in_late",1)
        
       ->count("check_in_late");
     

       

       $break_time_late_number = Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
       ->where("employeeinfos.id",$request->employee_id)
       ->whereRaw('MONTH(date) = ?',[$currentMonth])  
       ->whereRaw('YEAR(date) = ?',[$currentYear]) 
       ->where("resume_late",1)
       
       ->count("resume_late");

       $worked_days = Attend::join("employeeinfos","employeeinfos.id","attends.employee_id")
       ->where("employeeinfos.id",$request->employee_id)
       ->whereRaw('MONTH(date) = ?',[$currentMonth]) 
       ->whereRaw('YEAR(date) = ?',[$currentYear])  
       ->count("attends.employee_id");

       $approved_leave = LeaveInfo::join("employeeinfos","employeeinfos.id","leavesinfos.employee_id")
       ->where("employeeinfos.id",$request->employee_id)
       ->where(function ($query) use($request){
        if($request->start_date !=null  && $request->end_date!=null ) {$query->whereBetween("leavesinfos.created_at",[$request->start_date, $request->end_date]);}
         })
       ->whereRaw('MONTH(date) = ?',[$currentMonth]) 
       ->whereRaw('YEAR(date) = ?',[$currentYear])  
       ->where("leave_status",2)
         
       ->count("leavesinfos.employee_id");

       $absent_day = $monthlydays->expected_worked_days - ($approved_leave + $worked_days);
       //return $worked_days;

      



       $total_late= $check_in_late_number + $break_time_late_number;
       //return $total_late;
       $employee_salary_per_day = $employee->total_salary / $monthlydays->expected_worked_days;

       $expected_deduct = ($total_late * $company->amount) + ($absent_day * $employee_salary_per_day);
       $expected_deduct_days=  $absent_day * $company->amount_per_day;


       $salary_after_deduct = $employee->total_salary - ($expected_deduct);


        return response()->json([

            "success" => true,
            "employee"=>$employee,
            "regular_hours"=>$regular_hours,
            "worked_hours"=>$worked_hours,
            "check_in_late_number"=>$check_in_late_number,
            "break_time_late_number"=>$break_time_late_number,
            "worked_days"=>$worked_days,
            "total_late"=>$total_late,
            "total_salary"=>$employee->total_salary,
            "company_deduct_amount" =>$company->amount,
            "expected_deduct"=>$expected_deduct,
            "salary_after_deduct"=>$salary_after_deduct,
            "monthlydays"=>$monthlydays,
            "approved_leave"=>$approved_leave,
            "absent_day"=>$absent_day,
            "employee_salary_per_day"=>$employee_salary_per_day
            // "payroldetails"=>$payroldetails
            
            ]);
    
    }

    public function index(){
        $payrolls = PayRoll::all();
        return response()->json(["success"=>true,"payrolls"=>$payrolls]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

       $month= new Carbon($request->payrollmonth);
       
        //return $month;
        $payrol = new PayRoll;
        $payrol->title= $request->title;
        $payrol->date= $request->date;
        $payrol->note= $request->note;
        $payrol->total_salary= $request->total_salary;
        $payrol->total_deduct= $request->total_deduct;
        $payrol->total_amount= $request->total_amount;
        $payrol->payrollmonth= $month;

        if($payrol->save()){
            for($i=0;$i< count($request->employee); $i++){
                if($request->employee[$i]["employee_id"] > 0){

                
                $payroldetails = new PayRollDetail;
                $payroldetails->payroll_id = $payrol->id;
                $payroldetails->employee_id = $request->employee[$i]["employee_id"];
                $payroldetails->absent = $request->employee[$i]["absent"];
                $payroldetails->late_time = $request->employee[$i]["late_time"];
                $payroldetails->salary = $request->employee[$i]["salary"];
                $payroldetails->expected_deduct = $request->employee[$i]["expected_deduct"];
                $payroldetails->salary_after_deduct = $request->employee[$i]["salary_after_deduct"];
                $payroldetails->payrollmonth= $month;

                
                $payroldetails->save();
                }

            }
            return response()->json(["success"=>true,"msg"=>"successfully addes"]);
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
        $payrol =  PayRoll::select(DB::raw("monthname(date) as month,year(date) as year,payrolls.title,payrolls.date,payrolls.note,total_amount"))
        ->where("id",$id)
        ->first();
        $payroldetails = PayRollDetail::join("employeeinfos","employeeinfos.id","=","payroll_details.employee_id")
        ->where("payroll_id",$id)->get(array("payroll_details.*","employeeinfos.fullname"));
        return response()->json(["success"=>true,"payroll"=>$payrol,"payroldetails"=>$payroldetails]);
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
