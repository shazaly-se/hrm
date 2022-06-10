<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PermissionRole;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class RolesPermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rolesPermissions = PermissionRole::all();
        return response()->json([
        "success" => true,
        "message" => "Roles Permissions List",
        "data" => $rolesPermissions
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
        'role_id' => 'required',
        'permissions_id' => 'required'
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $rolesPermissions = RolesPermissions::create($input);
        return response()->json([
        "success" => true,
        "message" => "Roles Permissions created successfully.",
        "data" => $rolesPermissions
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RolesPermissions  $rolesPermissions
     * @return \Illuminate\Http\Response
     */
    public function show(RolesPermissions $rolesPermissions)
    {
        $rolesPermissions = RolesPermissions::find($id);
        if (is_null($rolesPermissions)) {
        return $this->sendError('Roles Permissions not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Roles Permissions retrieved successfully.",
        "data" => $rolesPermissions
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RolesPermissions  $rolesPermissions
     * @return \Illuminate\Http\Response
     */
    public function edit(RolesPermissions $rolesPermissions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RolesPermissions  $rolesPermissions
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'role_id' => 'required',
        'permissions_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $rolesPermissions = RolesPermissions::find($id);
        $rolesPermissions->role_id = $input['role_id'];
        $rolesPermissions->permissions_id = $input['permissions_id'];
        $rolesPermissions->updated_by = $user_id;
        $rolesPermissions->save();
        return response()->json([
        "success" => true,
        "message" => "Roles Permissions updated successfully.",
        "data" => $rolesPermissions
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RolesPermissions  $rolesPermissions
     * @return \Illuminate\Http\Response
     */
    public function destroy($roleId,$permissionId)
    {
        dd($permissionId);
        $rolesPermissions = PermissionRole::where([
            ['role_id', $roleId],
            ['permission_id', $permissionId]
        ]);
        $rolesPermissions->delete();
        return response()->json([
        "success" => true,
        "message" => "Roles Permissions deleted successfully.",
        "data" => $rolesPermissions
        ]);
    }
}
