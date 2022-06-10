<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $property = Property::all();
        return response()->json([
        "success" => true,
        "message" => "Property List",
        "data" => $property
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
        $id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        // 'name' => 'required',
        // 'tower_name' => 'required',
        // 'unit_number' => 'required',
        // 'floor_number' => 'required',
        // 'parking_slot' => 'required',
        // 'description' => 'required',
        // 'number_of_bedrooms' => 'required',
        // 'kitchen' => 'required',
        // 'hall' => 'required',
        // 'furnished' => 'required',
        // 'status' => 'required',
        // 'property_owner_id' => 'required',
        // 'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $input['created_by'] = $id;
        $input['updated_by'] = $id;
        $property = Property::create($input);
        return response()->json([
        "success" => true,
        "message" => "Property created successfully.",
        "data" => $property
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $property = Property::find($id);
        if (is_null($property)) {
        return $this->sendError('Property not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Property retrieved successfully.",
        "data" => $property
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\Response
     */
    public function edit(Property $property)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'name' => 'required',
        'tower_name' => 'required',
        'unit_number' => 'required',
        'floor_number' => 'required',
        'parking_slot' => 'required',
        'description' => 'required',
        'number_of_bedrooms' => 'required',
        'kitchen' => 'required',
        'hall' => 'required',
        'furnished' => 'required',
        'status' => 'required',
        'property_owner_id' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $property = Property::find($id);
        $property->name = $input['name'];
        $property->branch_id = $input['branch_id'];
        $property->updated_by = $user_id;
        $property->save();
        return response()->json([
        "success" => true,
        "message" => "Property updated successfully.",
        "data" => $property
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $property = Property::find($request->id);
        $property->delete();
        return response()->json([
        "success" => true,
        "message" => "Property deleted successfully.",
        "data" => $property
        ]);
    }
}
