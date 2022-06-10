<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\ContactsDeals;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class DealController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deal = Deal::all();
        return response()->json([
        "success" => true,
        "message" => "Deals List",
        "data" => $deal
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
        'deal_name' => 'required',
        'pipeline' => 'required',
        'deal_stage' => 'required',
        'amount' => 'required',
        'close_date' => 'required',
        'deal_owner' => 'required',
        'deal_type' => 'required',
        'priority' => 'required',
        'company_id' => 'required',
        'company_timeline' => 'required',
        'contact_id' => 'required',
        'contact_timeline' => 'required',
        'line_item' => 'required',
        'quantity' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $deal = Deal::create($input);
        $input['deal_id'] = $deal->id;
        $contactdeals = ContactsDeals::create($input);
        return response()->json([
        "success" => true,
        "message" => "Deal created successfully.",
        "data" => $deal
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Deal  $deal
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $deal = Deal::find($id);
        if (is_null($deal)) {
        return $this->sendError('Deal not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Deal retrieved successfully.",
        "data" => $deal
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Deal  $deal
     * @return \Illuminate\Http\Response
     */
    public function edit(Deal $deal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Deal  $deal
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'deal_name' => 'required',
        'pipeline' => 'required',
        'deal_stage' => 'required',
        'amount' => 'required',
        'close_date' => 'required',
        'deal_owner' => 'required',
        'deal_type' => 'required',
        'priority' => 'required',
        'company_id' => 'required',
        'company_timeline' => 'required',
        'contact_timeline' => 'required',
        'line_item' => 'required',
        'quantity' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $deal = Deal::find($id);
        $deal->deal_name = $input['deal_name'];
        $deal->pipeline = $input['pipeline'];
        $deal->deal_stage = $input['deal_stage'];
        $deal->amount = $input['amount'];
        $deal->close_date = $input['close_date'];
        $deal->deal_owner = $input['deal_owner'];
        $deal->deal_type = $input['deal_type'];
        $deal->priority = $input['priority'];
        $deal->company_id = $input['company_id'];
        $deal->company_timeline = $input['company_timeline'];
        $deal->contact_timeline = $input['contact_timeline'];
        $deal->line_item = $input['line_item'];
        $deal->quantity = $input['quantity'];
        $deal->updated_by = $user_id;
        $deal->save();
        return response()->json([
        "success" => true,
        "message" => "Deal updated successfully.",
        "data" => $deal
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Deal  $deal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $deal = Deal::find($request->id);
        $deal->delete();
        return response()->json([
        "success" => true,
        "message" => "Deal deleted successfully.",
        "data" => $deal
        ]);
    }
}
