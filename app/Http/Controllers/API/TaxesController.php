<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Taxes;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class TaxesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taxes = Taxes::all();
        return response()->json([
        "success" => true,
        "message" => "Taxes List",
        "data" => $taxes
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
        'tax_rate_name' => 'required',
        'tax_rate_percentage' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $taxes = Taxes::create($input);
        return response()->json([
        "success" => true,
        "message" => "Taxes created successfully.",
        "data" => $taxes
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Taxes  $taxes
     * @return \Illuminate\Http\Response
     */
    public function show(Taxes $taxes)
    {
        $taxes = Taxes::find($id);
        if (is_null($taxes)) {
        return $this->sendError('Taxes not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Taxes retrieved successfully.",
        "data" => $taxes
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Taxes  $taxes
     * @return \Illuminate\Http\Response
     */
    public function edit(Taxes $taxes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Taxes  $taxes
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'tax_rate_name' => 'required',
        'tax_rate_percentage' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $taxes = Taxes::find($id);
        $taxes->tax_rate_name = $input['tax_rate_name'];
        $taxes->tax_rate_percentage = $input['tax_rate_percentage'];
        $taxes->branch_id = $input['branch_id'];
        $taxes->updated_by = $user_id;
        $taxes->save();
        return response()->json([
        "success" => true,
        "message" => "Taxes updated successfully.",
        "data" => $taxes
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Taxes  $taxes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $taxes = Taxes::find($request->id);
        $taxes->delete();
        return response()->json([
        "success" => true,
        "message" => "Taxes deleted successfully.",
        "data" => $taxes
        ]);
    }
}
