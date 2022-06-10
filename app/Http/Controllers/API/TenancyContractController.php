<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TenancyContract;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class TenancyContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tenancyContract = TenancyContract::all();
        return response()->json([
        "success" => true,
        "message" => "Tenancy Contract List",
        "data" => $tenancyContract
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
        'access' => 'required',
        'contact' => 'required',
        'status' => 'required',
        'contract_date' => 'required',
        'start_date' => 'required',
        'expiry_date' => 'required',
        'security_check' => 'required',
        'duration' => 'required',
        'amount' => 'required',
        'payment_method' => 'required',
        'expiry_date' => 'required',
        'tasdeeq_username' => 'required',
        'tasdeeq_password' => 'required',
        'sewerage_premise_id' => 'required',
        'sewerage_username' => 'required',
        'sewerage_password' => 'required',
        'fewa_premise_id' => 'required',
        'fewa_username' => 'required',
        'fewa_password' => 'required',
        'notes' => 'required',
        'property_id' => 'required',
        'tenant_id' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $tenancyContract = TenancyContract::create($input);

// mail sending


$details = [
    'title' => 'Mail from Find Properties',
    'body' => 'This is for testing email using smtp'
];

\Mail::to('1k96renju@gmail.com')->send(new \App\Mail\TenancyContractMail($details));

dd("Email is Sent.");


        return response()->json([
        "success" => true,
        "message" => "Tenancy Contract created successfully.",
        "data" => $tenancyContract
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TenancyContract  $tenancyContract
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tenancyContract = TenancyContract::find($id);
        if (is_null($tenancyContract)) {
        return $this->sendError('Tenancy Contract not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Tenancy Contract retrieved successfully.",
        "data" => $tenancyContract
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TenancyContract  $tenancyContract
     * @return \Illuminate\Http\Response
     */
    public function edit(TenancyContract $tenancyContract)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TenancyContract  $tenancyContract
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'access' => 'required',
        'contact' => 'required',
        'status' => 'required',
        'contract_date' => 'required',
        'start_date' => 'required',
        'expiry_date' => 'required',
        'security_check' => 'required',
        'duration' => 'required',
        'amount' => 'required',
        'payment_method' => 'required',
        'expiry_date' => 'required',
        'tasdeeq_username' => 'required',
        'tasdeeq_password' => 'required',
        'sewerage_premise_id' => 'required',
        'sewerage_username' => 'required',
        'sewerage_password' => 'required',
        'fewa_premise_id' => 'required',
        'fewa_username' => 'required',
        'fewa_password' => 'required',
        'notes' => 'required',
        'property_id' => 'required',
        'tenant_id' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $tenancyContract = TenancyContract::find($id);
        $tenancyContract->access = $input['access'];
        $tenancyContract->contact = $input['contact'];
        $tenancyContract->status = $input['status'];
        $tenancyContract->contract_date = $input['contract_date'];
        $tenancyContract->start_date = $input['start_date'];
        $tenancyContract->expiry_date = $input['expiry_date'];
        $tenancyContract->security_check = $input['security_check'];
        $tenancyContract->duration = $input['duration'];
        $tenancyContract->amount = $input['amount'];
        $tenancyContract->payment_method = $input['payment_method'];
        $tenancyContract->expiry_date = $input['expiry_date'];
        $tenancyContract->tasdeeq_username = $input['tasdeeq_username'];
        $tenancyContract->tasdeeq_password = $input['tasdeeq_password'];
        $tenancyContract->sewerage_premise_id = $input['sewerage_premise_id'];
        $tenancyContract->sewerage_username = $input['sewerage_username'];
        $tenancyContract->sewerage_password = $input['sewerage_password'];
        $tenancyContract->fewa_premise_id = $input['fewa_premise_id'];
        $tenancyContract->fewa_username = $input['fewa_username'];
        $tenancyContract->fewa_password = $input['fewa_password'];
        $tenancyContract->notes = $input['notes'];
        $tenancyContract->property_id = $input['property_id'];
        $tenancyContract->tenant_id = $input['tenant_id'];
        $tenancyContract->branch_id = $input['branch_id'];
        $tenancyContract->updated_by = $user_id;
        $tenancyContract->save();
        return response()->json([
        "success" => true,
        "message" => "Tenancy Contract updated successfully.",
        "data" => $tenancyContract
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TenancyContract  $tenancyContract
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $tenancyContract = TenancyContract::find($request->id);
        $tenancyContract->delete();
        return response()->json([
        "success" => true,
        "message" => "Tenancy Contract deleted successfully.",
        "data" => $tenancyContract
        ]);
    }
}
