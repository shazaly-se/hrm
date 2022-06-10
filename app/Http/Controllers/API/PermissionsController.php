<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
//use App\Models\Permission;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::all();
        // return response()->json([
        // "success" => true,
        // "message" => "Permissions List",
        // "permissions" => $permissions
        // ]);
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
        $permission = new Permission;
        $permission->name= $request->name;
        $permission->guard_name="web";
        $permission->save();
        return "fhf";
        return response()->json([
        "success" => true,
        "message" => "Permissions created successfully.",
        "data" => $permission
        ]);

        // $id = Auth::id();
        // $input = $request->all();
        // $validator = Validator::make($input, [
        // 'name' => 'required',
        // ]);
        // if($validator->fails()){
        // return $this->sendError('Validation Error.', $validator->errors());       
        // }
        // $input['created_by'] = $id;
        // $input['updated_by'] = $id;
        // $permissions = Permission::create($input);
        // return response()->json([
        // "success" => true,
        // "message" => "Permissions created successfully.",
        // "data" => $permissions
        // ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Permissions  $permissions
     * @return \Illuminate\Http\Response
     */
    public function show(Permissions $permissions)
    {
        $permissions = Permissions::find($id);
        if (is_null($permissions)) {
        return $this->sendError('Permissions not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Permissions retrieved successfully.",
        "data" => $permissions
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Permissions  $permissions
     * @return \Illuminate\Http\Response
     */
    public function edit(Permissions $permissions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permissions  $permissions
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'name' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $permissions = Permissions::find($id);
        $permissions->name = $input['name'];
        $permissions->value = $input['value'];
        $permissions->label = $input['label'];
        $permissions->save();
        return response()->json([
        "success" => true,
        "message" => "Permissions updated successfully.",
        "data" => $permissions
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Permissions  $permissions
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $permissions = Permissions::find($request->id);
        $permissions->delete();
        return response()->json([
        "success" => true,
        "message" => "Permissions deleted successfully.",
        "data" => $permissions
        ]);
    }
}
