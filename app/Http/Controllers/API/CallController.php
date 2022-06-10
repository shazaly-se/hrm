<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Call;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class CallController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $call = Call::all();
        return response()->json([
        "success" => true,
        "message" => "Call List",
        "data" => $call
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
        // dd($request->all());
        $input = $request->all();
        $validator = Validator::make($input, [
        'call_title' => 'required',
        'activity_date' => 'required',
        'activity_assigned_to' => 'required',
        'call_notes' => 'required',
        'call_outcome' => 'required',
        'transcript_available' => 'required',
        'call_duration' => 'required',
        'contact_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $call = Call::create($input);
        return response()->json([
        "success" => true,
        "message" => "Call created successfully.",
        "data" => $call
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Call  $call
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // dd('hi');
        $call = Call::find($id);
        if (is_null($call)) {
        return $this->sendError('Call not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Call retrieved successfully.",
        "data" => $call
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Call  $call
     * @return \Illuminate\Http\Response
     */
    public function edit(Call $call)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Call  $call
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'assigned_by_id' => 'required',
        'assigned_to_id' => 'required',
        'customer_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $call = Call::find($id);
        $call->assigned_by_id = $input['assigned_by_id'];
        $call->assigned_to_id = $input['assigned_to_id'];
        $call->customer_id = $input['customer_id'];
        $call->updated_by = $user_id;
        $call->save();
        return response()->json([
        "success" => true,
        "message" => "Call updated successfully.",
        "data" => $call
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Call  $call
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $call = Call::find($request->id);
        $call->delete();
        return response()->json([
        "success" => true,
        "message" => "Call deleted successfully.",
        "data" => $call
        ]);
    }

    public function filterByInputs(Request $request)
    {
        $call_title = $request->call_title;
        $call_duration = $request->call_duration;
        
        $call = Call::query();
            if($call_title)
            {
                $call = $call->where('call_title',$call_title);
            }
            if($call_duration)
            {
                $call = $call->where('call_duration',$call_duration);
            }
            
        $call = $call->get();

        return response()->json([
            "success" => true,
            "message" => "Call filterd successfully.",
            "data" => $call
            ]);
    }
}
