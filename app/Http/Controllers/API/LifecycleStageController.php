<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LifecycleStage;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class LifecycleStageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lifecycleStage = LifecycleStage::all();
        return response()->json([
        "success" => true,
        "message" => "Lifecycle Stage List",
        "data" => $lifecycleStage
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
        $lifecycleStage = LifecycleStage::create($input);
        return response()->json([
        "success" => true,
        "message" => "Lifecycle Stage created successfully.",
        "data" => $lifecycleStage
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LifecycleStage  $lifecycleStage
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lifecycleStage = LifecycleStage::find($id);
        if (is_null($lifecycleStage)) {
        return $this->sendError('Lifecycle Stage not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Lifecycle Stage retrieved successfully.",
        "data" => $lifecycleStage
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LifecycleStage  $lifecycleStage
     * @return \Illuminate\Http\Response
     */
    public function edit(LifecycleStage $lifecycleStage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LifecycleStage  $lifecycleStage
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
        $lifecycleStage = LifecycleStage::find($id);
        $lifecycleStage->name = $input['name'];
        $lifecycleStage->branch_id = $input['branch_id'];
        $lifecycleStage->updated_by = $user_id;
        $lifecycleStage->save();
        return response()->json([
        "success" => true,
        "message" => "Lifecycle Stage updated successfully.",
        "data" => $lifecycleStage
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LifecycleStage  $lifecycleStage
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $lifecycleStage = LifecycleStage::find($request->id);
        $lifecycleStage->delete();
        return response()->json([
        "success" => true,
        "message" => "Lifecycle Stage deleted successfully.",
        "data" => $lifecycleStage
        ]);
    }
}
