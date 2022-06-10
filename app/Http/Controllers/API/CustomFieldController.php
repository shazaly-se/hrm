<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class CustomFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customField = CustomField::all();
        return response()->json([
        "success" => true,
        "message" => "Custom Field List",
        "data" => $customField
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
        'custom_field_name' => 'required',
        'type_id' => 'required',
        'module_id' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $customField = CustomField::create($input);
        return response()->json([
        "success" => true,
        "message" => "Custom Field created successfully.",
        "data" => $customField
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomField  $customField
     * @return \Illuminate\Http\Response
     */
    public function show(CustomField $customField)
    {
        $customField = CustomField::find($id);
        if (is_null($customField)) {
        return $this->sendError('Custom Field not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Custom Field retrieved successfully.",
        "data" => $customField
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomField  $customField
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomField $customField)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomField  $customField
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'custom_field_name' => 'required',
        'type_id' => 'required',
        'module_id' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $customField = CustomField::find($id);
        $customField->custom_field_name = $input['custom_field_name'];
        $customField->type_id = $input['type_id'];
        $customField->module_id = $input['module_id'];
        $customField->branch_id = $input['branch_id'];
        $customField->updated_by = $user_id;
        $customField->save();
        return response()->json([
        "success" => true,
        "message" => "Custom Field updated successfully.",
        "data" => $customField
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomField  $customField
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $customField = CustomField::find($request->id);
        $customField->delete();
        return response()->json([
        "success" => true,
        "message" => "Custom Field deleted successfully.",
        "data" => $customField
        ]);
    }
}
