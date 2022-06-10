<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BankDetails;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class BankDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches = BankDetails::all();
        return response()->json([
        "success" => true,
        "message" => "Bank Details Branches List",
        "data" => $branches
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
        'employee_id' => 'required',
        'bank_holder_name' => 'required',
        'bank_name' => 'required',
        'account_number' => 'required',
        'opening_balance' => 'required',
        'contact_number' => 'required',
        'bank_address' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $input['created_by'] = $id;
        $input['updated_by'] = $id;
        $bankDetails = BankDetails::create($input);
        return response()->json([
        "success" => true,
        "message" => "Bank Details created successfully.",
        "data" => $bankDetails
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BankDetails  $bankDetails
     * @return \Illuminate\Http\Response
     */
    public function show(BankDetails $bankDetails)
    {
        $bankDetails = BankDetails::find($id);
        if (is_null($bankDetails)) {
        return $this->sendError('Bank Details not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Bank Details retrieved successfully.",
        "data" => $bankDetails
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BankDetails  $bankDetails
     * @return \Illuminate\Http\Response
     */
    public function edit(BankDetails $bankDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BankDetails  $bankDetails
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'employee_id' => 'required',
        'bank_holder_name' => 'required',
        'bank_name' => 'required',
        'account_number' => 'required',
        'opening_balance' => 'required',
        'contact_number' => 'required',
        'bank_address' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $bankDetails = BankDetails::find($id);
        $bankDetails->employee_id = $input['employee_id'];
        $bankDetails->bank_holder_name = $input['bank_holder_name'];
        $bankDetails->bank_name = $input['bank_name'];
        $bankDetails->account_number = $input['account_number'];
        $bankDetails->opening_balance = $input['opening_balance'];
        $bankDetails->contact_number = $input['contact_number'];
        $bankDetails->bank_address = $input['bank_address'];
        $bankDetails->branch_id = $input['branch_id'];
        $bankDetails->updated_by = $user_id;
        $bankDetails->save();
            return response()->json([
                "success" => true,
                "message" => "Branch updated successfully.",
                "data" => $bankDetails
                ]); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankDetails  $bankDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $bankDetails = BankDetails::find($request->id);
        $bankDetails->delete();
        return response()->json([
        "success" => true,
        "message" => "Bank Details deleted successfully.",
        "data" => $bankDetails
        ]);
    }

    public function filterByInputs(Request $request)
    {
        $employee_id = $request->employee_id;
        $bank_holder_name = $request->bank_holder_name;
        $bank_name = $request->bank_name;
        $account_number = $request->account_number;
        $opening_balance = $request->opening_balance;
        $contact_number = $request->contact_number;
        $bank_address = $request->bank_address;
        
        $bankDetails = BankDetails::query();
            if($employee_id)
            {
                $bankDetails = $bankDetails->where('employee_id',$employee_id);
            }
            if($bank_holder_name)
            {
                $bankDetails = $bankDetails->where('bank_holder_name',$bank_holder_name);
            }
            if($bank_name)
            {
                $bankDetails = $bankDetails->where('bank_name',$bank_name);
            }
            if($account_number)
            {
                $bankDetails = $bankDetails->where('account_number',$account_number);
            } 
            if($opening_balance)
            {
                $bankDetails = $bankDetails->where('opening_balance',$opening_balance);
            }
            if($contact_number)
            {
                $bankDetails = $bankDetails->where('contact_number',$contact_number);
            } 
            if($bank_address)
            {
                $bankDetails = $bankDetails->where('bank_address',$bank_address);
            }
            
        $bankDetails = $bankDetails->get();

        return response()->json([
            "success" => true,
            "message" => "Bank Details filterd successfully.",
            "data" => $bankDetails
            ]);
    }
}
