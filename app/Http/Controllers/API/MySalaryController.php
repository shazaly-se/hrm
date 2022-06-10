<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PayRollDetail;
use App\Models\EmployeeInfo;
use DB;
class MySalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $user = auth()->user();
        
        $employee= EmployeeInfo::where("user_id",$user->id)->first();
        $salay= PayRollDetail::where("employee_id",$employee->id)
        ->orderBy('id', 'DESC')->take(6) 
        ->select("absent","late_time","salary","expected_deduct","salary_after_deduct",DB::raw('MONTHNAME(payrollmonth) as monthname'),DB::raw('Year(payrollmonth) as year'))->get();

     
        
        return response()->json(["salary"=>$salay]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
