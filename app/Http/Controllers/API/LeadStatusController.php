<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LeadStatus;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class LeadStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leadStatus = LeadStatus::all();
        return response()->json([
        "success" => true,
        "message" => "Lead Status List",
        "data" => $leadStatus
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
        'name' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $leadStatus = LeadStatus::create($input);
        return response()->json([
        "success" => true,
        "message" => "Lead Status created successfully.",
        "data" => $leadStatus
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LeadStatus  $leadStatus
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $leadStatus = LeadStatus::find($id);
        if (is_null($leadStatus)) {
        return $this->sendError('Lead Status not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Lead Status retrieved successfully.",
        "data" => $leadStatus
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LeadStatus  $leadStatus
     * @return \Illuminate\Http\Response
     */
    public function edit(LeadStatus $leadStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LeadStatus  $leadStatus
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'name' => 'required',
        // 'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $leadStatus = LeadStatus::find($id);
        // dd($leadStatus->all());
        $leadStatus->name = $input['name'];
        $leadStatus->branch_id = $input['branch_id'];
        $leadStatus->updated_by = $user_id;
        $leadStatus->save();
        return response()->json([
        "success" => true,
        "message" => "Lead Status updated successfully.",
        "data" => $leadStatus
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LeadStatus  $leadStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $deal = LeadStatus::find($request->id);
        $leadStatus->delete();
        return response()->json([
        "success" => true,
        "message" => "Lead Status deleted successfully.",
        "data" => $leadStatus
        ]);
    }
}
