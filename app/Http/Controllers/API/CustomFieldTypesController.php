<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CustomFieldTypes;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class CustomFieldTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customFieldTypes = CustomFieldTypes::all();
        return response()->json([
        "success" => true,
        "message" => "Custom Field Types List",
        "data" => $customFieldTypes
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
        'custom_field_type' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $customFieldTypes = CustomFieldTypes::create($input);
        return response()->json([
        "success" => true,
        "message" => "Custom Field Types created successfully.",
        "data" => $customFieldTypes
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomFieldTypes  $customFieldTypes
     * @return \Illuminate\Http\Response
     */
    public function show(CustomFieldTypes $customFieldTypes)
    {
        $customFieldTypes = CustomFieldTypes::find($id);
        if (is_null($customFieldTypes)) {
        return $this->sendError('Custom Field Types not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Custom Field Types retrieved successfully.",
        "data" => $customFieldTypes
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomFieldTypes  $customFieldTypes
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomFieldTypes $customFieldTypes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomFieldTypes  $customFieldTypes
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'custom_field_type' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $customFieldTypes = CustomFieldTypes::find($id);
        $customFieldTypes->custom_field_type = $input['custom_field_type'];
        $customFieldTypes->branch_id = $input['branch_id'];
        $customFieldTypes->updated_by = $user_id;
        $customFieldTypes->save();
        return response()->json([
        "success" => true,
        "message" => "Custom Field Types updated successfully.",
        "data" => $customFieldTypes
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomFieldTypes  $customFieldTypes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $customFieldTypes = CustomFieldTypes::find($request->id);
        $customFieldTypes->delete();
        return response()->json([
        "success" => true,
        "message" => "Custom Field Types deleted successfully.",
        "data" => $customFieldTypes
        ]);
    }
}
