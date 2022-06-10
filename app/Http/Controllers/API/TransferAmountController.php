<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TransferAmount;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class TransferAmountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transferAmount = TransferAmount::all();
        return response()->json([
        "success" => true,
        "message" => "Transfer Amount Branches List",
        "data" => $transferAmount
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
        $user = Auth::user();
        $id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'from_account' => 'required',
        'to_account' => 'required',
        'amount' => 'required',
        'date' => 'required',
        'reference' => 'required',
        'description' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $input['created_by'] = $id;
        $input['updated_by'] = $id;
        $transferAmount = TransferAmount::create($input);
        return response()->json([
        "success" => true,
        "message" => "Transfer Amount created successfully.",
        "data" => $transferAmount
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TransferAmount  $transferAmount
     * @return \Illuminate\Http\Response
     */
    public function show(TransferAmount $transferAmount)
    {
        $transferAmount = TransferAmount::find($id);
        if (is_null($transferAmount)) {
        return $this->sendError('Transfer Amount not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Transfer Amount retrieved successfully.",
        "data" => $transferAmount
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransferAmount  $transferAmount
     * @return \Illuminate\Http\Response
     */
    public function edit(TransferAmount $transferAmount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TransferAmount  $transferAmount
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'from_account' => 'required',
        'to_account' => 'required',
        'amount' => 'required',
        'date' => 'required',
        'reference' => 'required',
        'description' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $transferAmount = TransferAmount::find($id);
        $transferAmount->from_account = $input['from_account'];
        $transferAmount->to_account = $input['to_account'];
        $transferAmount->amount = $input['amount'];
        $transferAmount->date = $input['date'];
        $transferAmount->reference = $input['reference'];
        $transferAmount->description = $input['description'];
        $transferAmount->branch_id = $input['branch_id'];
        $transferAmount->updated_by = $user_id;
        $transferAmount->save();
            return response()->json([
                "success" => true,
                "message" => "Transfer Amount updated successfully.",
                "data" => $transferAmount
                ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TransferAmount  $transferAmount
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $transferAmount = TransferAmount::find($request->id);
        $transferAmount->delete();
        return response()->json([
        "success" => true,
        "message" => "Transfer Amount deleted successfully.",
        "data" => $transferAmount
        ]);
    }
}
