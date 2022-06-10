<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeAttendance;
use App\Models\AttendsExist;

use DB;
class EmployeeAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attends= EmployeeAttendance::orderBy("date","asc")
        ->get();
      // return $attends;

        // foreach($attends as $key => $attend  ){
        //     return $attend;
        //    // dd($attend);
         
        //     $attendsexist = AttendsExist::where("employee_id",$attend->id)
        //     ->first();
        //     if($attendsexist);
        //                 //return $attendsexist;

        // }

        foreach ($attends as $key => $attend) {

            

            // for ($i=0; $i < count($attends); $i++) { 
            //     if($attend->id == $attends[$i]->id){
            //         print  $attend->id . '"';
            //     }

            //    // print '"' . $key . '" = "' . $attends[$i] . '"';
            // }
    
        }


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
