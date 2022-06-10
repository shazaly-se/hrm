<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContactOwner;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class ContactOwnersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contactOwner = ContactOwner::all();
        return response()->json([
        "success" => true,
        "message" => "Contact Owners List",
        "data" => $contactOwner
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
        'email' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $contactOwner = ContactOwner::create($input);
        return response()->json([
        "success" => true,
        "message" => "Contact Owners created successfully.",
        "data" => $contactOwner
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ContactOwner  $contactOwner
     * @return \Illuminate\Http\Response
     */
    public function show(ContactOwner $contactOwner)
    {
        $contactOwner = ContactOwner::find($id);
        if (is_null($contactOwner)) {
        return $this->sendError('Contact Owners not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Contact Owners retrieved successfully.",
        "data" => $contactOwner
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ContactOwner  $contactOwner
     * @return \Illuminate\Http\Response
     */
    public function edit(ContactOwner $contactOwner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ContactOwner  $contactOwner
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'name' => 'required',
        'email' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $contactOwner = ContactOwner::find($id);
        $contactOwner->name = $input['name'];
        $contactOwner->email = $input['email'];
        $contactOwner->updated_by = $user_id;
        $contactOwner->save();
        return response()->json([
        "success" => true,
        "message" => "Contact Owners updated successfully.",
        "data" => $contactOwner
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ContactOwner  $contactOwner
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $contactOwner = ContactOwner::find($request->id);
        $contactOwner->delete();
        return response()->json([
        "success" => true,
        "message" => "Contact Owners deleted successfully.",
        "data" => $contactOwner
        ]);
    }
}
