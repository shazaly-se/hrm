<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attendance = Attendance::all();
        return response()->json([
        "success" => true,
        "message" => "Attendance List",
        "data" => $attendance
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
        $input = $request->all();
        $validator = Validator::make($input, [
        'date' => 'required',
        'total_hours' => 'required',
        'employee_id' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $attendance = Attendance::create($input);
        return response()->json([
        "success" => true,
        "message" => "Attendance created successfully.",
        "data" => $attendance
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        $attendance = Attendance::find($id);
        if (is_null($attendance)) {
        return $this->sendError('Attendance not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Attendance retrieved successfully.",
        "data" => $attendance
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'date' => 'required',
        'total_hours' => 'required',
        'employee_id' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $attendance = Attendance::find($id);
        $attendance->date = $input['date'];
        $attendance->total_hours = $input['total_hours'];
        $attendance->employee_id = $input['employee_id'];
        $attendance->branch_id = $input['branch_id'];
        $attendance->updated_by = $user_id;
        $attendance->save();
        return response()->json([
        "success" => true,
        "message" => "Attendance updated successfully.",
        "data" => $attendance
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $attendance = Attendance::find($request->id);
        $attendance->delete();
        return response()->json([
        "success" => true,
        "message" => "Attendance deleted successfully.",
        "data" => $attendance
        ]);
    }

    public function filterByInputs(Request $request)
    {
        $date = $request->date;
        $total_hours = $request->total_hours;
        $employee_id = $request->employee_id;
        
        $attendance = Attendance::query();
            if($date)
            {
                $attendance = $attendance->where('date',$date);
            }
            if($total_hours)
            {
                $attendance = $attendance->where('total_hours',$total_hours);
            }
            if($employee_id)
            {
                $attendance = $attendance->where('employee_id',$employee_id);
            }
            
        $attendance = $attendance->get();

        return response()->json([
            "success" => true,
            "message" => "Attendance filterd successfully.",
            "data" => $attendance
            ]);
    }
}
