<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Commissions;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class CommissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $commissions = Commissions::all();
        return response()->json([
        "success" => true,
        "message" => "Commissions List",
        "data" => $commissions
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
        'commission' => 'required',
        'date' => 'required',
        'property_id' => 'required',
        'tenant_id' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $commissions = Commissions::create($input);
        return response()->json([
        "success" => true,
        "message" => "Commissions created successfully.",
        "data" => $commissions
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Commissions  $commissions
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $commissions = Commissions::find($id);
        if (is_null($commissions)) {
        return $this->sendError('Commissions not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Commissions retrieved successfully.",
        "data" => $commissions
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Commissions  $commissions
     * @return \Illuminate\Http\Response
     */
    public function edit(Commissions $commissions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Commissions  $commissions
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'commission' => 'required',
        'date' => 'required',
        'property_id' => 'required',
        'tenant_id' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $commissions = Commissions::find($id);
        $commissions->commission = $input['commission'];
        $commissions->date = $input['date'];
        $commissions->property_id = $input['property_id'];
        $commissions->tenant_id = $input['tenant_id'];
        $commissions->branch_id = $input['branch_id'];
        $commissions->updated_by = $user_id;
        $commissions->save();
        return response()->json([
        "success" => true,
        "message" => "Commissions updated successfully.",
        "data" => $commissions
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Commissions  $commissions
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $commissions = Commissions::find($request->id);
        $commissions->delete();
        return response()->json([
        "success" => true,
        "message" => "Commissions deleted successfully.",
        "data" => $commissions
        ]);
    }

    public function filterByInputs(Request $request)
    {
        $commission = $request->commission;
        $date = $request->date;
        $property_id = $request->property_id;
        $tenant_id = $request->tenant_id;
        
        $commissions = Commissions::query();
            if($commission)
            {
                $commissions = $commissions->where('commission',$commission);
            }
            if($date)
            {
                $commissions = $commissions->where('date',$date);
            }
            if($property_id)
            {
                $commissions = $commissions->where('property_id',$property_id);
            }
            if($tenant_id)
            {
                $commissions = $commissions->where('tenant_id',$tenant_id);
            }
        $commissions = $commissions->get();

        return response()->json([
            "success" => true,
            "message" => "Commissions filterd successfully.",
            "data" => $commissions
            ]);
    }
}
