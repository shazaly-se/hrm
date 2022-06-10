<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teamMember = TeamMember::all();
        return response()->json([
        "success" => true,
        "message" => "Team Member List",
        "data" => $teamMember
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
        'team_id' => 'required',
        'member_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $teamMember = TeamMember::create($input);
        return response()->json([
        "success" => true,
        "message" => "Team Member created successfully.",
        "data" => $teamMember
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TeamMember  $teamMember
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $teamMember = TeamMember::find($id);
        if (is_null($teamMember)) {
        return $this->sendError('Team Member not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Team Member retrieved successfully.",
        "data" => $teamMember
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TeamMember  $teamMember
     * @return \Illuminate\Http\Response
     */
    public function edit(TeamMember $teamMember)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TeamMember  $teamMember
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'team_id' => 'required',
        'member_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $teamMember = TeamMember::find($id);
        $teamMember->team_id = $input['team_id'];
        $teamMember->member_id = $input['member_id'];
        $teamMember->updated_by = $user_id;
        $teamMember->save();
        return response()->json([
        "success" => true,
        "message" => "Team Member updated successfully.",
        "data" => $teamMember
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TeamMember  $teamMember
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $teamMember = TeamMember::find($request->id);
        $teamMember->delete();
        return response()->json([
        "success" => true,
        "message" => "Team Member deleted successfully.",
        "data" => $teamMember
        ]);
    }
}
