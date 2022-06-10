<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Punching;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class PunchingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $punching = Punching::all();
        return response()->json([
        "success" => true,
        "message" => "Punching List",
        "data" => $punching
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
        'punch_in' => 'required',
        'punch_out' => 'required',
        'employee_id' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $punching = Punching::create($input);
        return response()->json([
        "success" => true,
        "message" => "Punching created successfully.",
        "data" => $punching
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Punching  $punching
     * @return \Illuminate\Http\Response
     */
    public function show(Punching $punching)
    {
        $punching = Punching::find($id);
        if (is_null($punching)) {
        return $this->sendError('Punching not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Punching retrieved successfully.",
        "data" => $punching
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Punching  $punching
     * @return \Illuminate\Http\Response
     */
    public function edit(Punching $punching)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Punching  $punching
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'date' => 'required',
        'punch_in' => 'required',
        'punch_out' => 'required',
        'employee_id' => 'required',
        'branch_id' => 'required',
        
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $punching = Punching::find($id);
        $punching->date = $input['date'];
        $punching->punch_in = $input['punch_in'];
        $punching->punch_out = $input['punch_out'];
        $punching->employee_id = $input['employee_id'];
        $punching->branch_id = $input['branch_id'];
        $punching->updated_by = $user_id;
        $punching->save();
        return response()->json([
        "success" => true,
        "message" => "Punching updated successfully.",
        "data" => $punching
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Punching  $punching
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $punching = Punching::find($request->id);
        $punching->delete();
        return response()->json([
        "success" => true,
        "message" => "Punching deleted successfully.",
        "data" => $punching
        ]);
    }
}
