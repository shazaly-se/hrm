<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoice = Invoice::all();
        return response()->json([
        "success" => true,
        "message" => "Invoice List",
        "data" => $invoice
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
        'customer_id' => 'required',
        'issue_date' => 'required',
        'due_date' => 'required',
        'invoice_number' => 'required',
        'category' => 'required',
        'reference_number' => 'required',
        'discount_apply' => 'required',
        'sku' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $input['created_by'] = $id;
        $input['updated_by'] = $id;
        $invoice = Invoice::create($input);
        return response()->json([
        "success" => true,
        "message" => "Invoice created successfully.",
        "data" => $invoice
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        $invoice = Invoice::find($id);
        if (is_null($invoice)) {
        return $this->sendError('Invoice not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Invoice retrieved successfully.",
        "data" => $invoice
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'customer_id' => 'required',
        'issue_date' => 'required',
        'due_date' => 'required',
        'invoice_number' => 'required',
        'category' => 'required',
        'reference_number' => 'required',
        'discount_apply' => 'required',
        'sku' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $invoice = Invoice::find($id);
        $invoice->customer_id = $input['customer_id'];
        $invoice->issue_date = $input['issue_date'];
        $invoice->due_date = $input['due_date'];
        $invoice->invoice_number = $input['invoice_number'];
        $invoice->category = $input['category'];
        $invoice->reference_number = $input['reference_number'];
        $invoice->discount_apply = $input['discount_apply'];
        $invoice->sku = $input['sku'];
        $invoice->branch_id = $input['branch_id'];
        $invoice->updated_by = $user_id;
        $invoice->save();
            return response()->json([
                "success" => true,
                "message" => "Invoice updated successfully.",
                "data" => $invoice
                ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $invoice = Invoice::find($request->id);
        $invoice->delete();
        return response()->json([
        "success" => true,
        "message" => "Invoice deleted successfully.",
        "data" => $invoice
        ]);
    }
}
