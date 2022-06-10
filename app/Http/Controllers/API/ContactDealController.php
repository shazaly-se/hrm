<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContactsDeals;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class ContactDealController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contactsDeals = ContactsDeals::all();
        return response()->json([
        "success" => true,
        "message" => "Contact Deals List",
        "data" => $contactsDeals
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
        'contact_id' => 'required',
        'deal_id' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $contactsDeals = ContactsDeals::create($input);
        return response()->json([
        "success" => true,
        "message" => "Contact Deals created successfully.",
        "data" => $contactsDeals
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ContactsDeals  $contactsDeals
     * @return \Illuminate\Http\Response
     */
    public function show(ContactsDeals $contactsDeals)
    {
        $contactsDeals = ContactsDeals::find($id);
        if (is_null($task)) {
        return $this->sendError('Contact Deals not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Contact Deals retrieved successfully.",
        "data" => $contactsDeals
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ContactsDeals  $contactsDeals
     * @return \Illuminate\Http\Response
     */
    public function edit(ContactsDeals $contactsDeals)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ContactsDeals  $contactsDeals
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'contact_id' => 'required',
        'deal_id' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $contactsDeals = ContactsDeals::find($id);
        $contactsDeals->contact_id = $input['contact_id'];
        $contactsDeals->deal_id = $input['deal_id'];
        $contactsDeals->branch_id = $input['branch_id'];
        $contactsDeals->updated_by = $user_id;
        $contactsDeals->save();
        return response()->json([
        "success" => true,
        "message" => "Contact Deals updated successfully.",
        "data" => $contactsDeals
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ContactsDeals  $contactsDeals
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $contactsDeals = ContactsDeals::find($request->id);
        $contactsDeals->delete();
        return response()->json([
        "success" => true,
        "message" => "Contact Deals deleted successfully.",
        "data" => $contactsDeals
        ]);
    }
    public function filterByInputs(Request $request)
    {
        $contact_id = $request->contact_id;
        $deal_id = $request->deal_id;
        
        $contact = Contact::query();
            if($contact_id)
            {
                $contact = $contact->where('contact_id',$contact_id);
            }
            if($first_name)
            {
                $contact = $contact->where('first_name',$first_name);
            }
            
        $contact = $contact->get();

        return response()->json([
            "success" => true,
            "message" => "Contact filterd successfully.",
            "data" => $contact
            ]);
    }
}
