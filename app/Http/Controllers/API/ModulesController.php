<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Modules;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class ModulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modules = Modules::all();
        return response()->json([
        "success" => true,
        "message" => "Modules List",
        "data" => $modules
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
        'module_name' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $modules = Modules::create($input);
        return response()->json([
        "success" => true,
        "message" => "Modules created successfully.",
        "data" => $modules
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modules  $modules
     * @return \Illuminate\Http\Response
     */
    public function show(Modules $modules)
    {
        $modules = Modules::find($id);
        if (is_null($modules)) {
        return $this->sendError('Modules not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Modules retrieved successfully.",
        "data" => $modules
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Modules  $modules
     * @return \Illuminate\Http\Response
     */
    public function edit(Modules $modules)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modules  $modules
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'module_name' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $modules = Modules::find($id);
        $modules->module_name = $input['module_name'];
        $modules->branch_id = $input['branch_id'];
        $modules->updated_by = $user_id;
        $modules->save();
        return response()->json([
        "success" => true,
        "message" => "Modules updated successfully.",
        "data" => $modules
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modules  $modules
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $modules = Modules::find($request->id);
        $modules->delete();
        return response()->json([
        "success" => true,
        "message" => "Modules deleted successfully.",
        "data" => $modules
        ]);
    }
}
