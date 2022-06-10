<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PropertyOwner;
use App\Models\Employee;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class PropertyOwnersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $propertyOwner = Employee::where('status','1')->get();
        return response()->json([
        "success" => true,
        "message" => "Property Owners List",
        "data" => $propertyOwner
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
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
            // 'name' => 'required',
            // 'contact' => 'required',
            // 'emirates_id' => 'required',
            // 'passport_number' => 'required',
            // 'nationality' => 'required',
            // 'email_address' => 'required',
            // 'emirates_id_file' => 'required|image|file|mimes:jpeg,png,jpg,gif',
            // 'passport' => 'required|image|file|mimes:jpeg,png,jpg,gif',
            // 'branch_id' => 'required',
        ]);
        
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        //Emirates ID upload
        if ($emirates_id_file = $request->file('emirates_id_file')) {
            $destinationPath = 'images/propertyOwner/emirates_id_file';
            $emirates_id_Image = date('YmdHis') . "." . $emirates_id_file->getClientOriginalExtension();
            $emirates_id_file->move($destinationPath, $emirates_id_Image);
            $input['emirates_id_file'] = "$emirates_id_Image";
        }

        //Passport upload
        if ($passport = $request->file('passport')) {
            $destinationPath = 'images/propertyOwner/passport';
            $passportImage = date('YmdHis') . "." . $passport->getClientOriginalExtension();
            $passport->move($destinationPath, $passportImage);
            $input['passport'] = "$passportImage";
        }
        $input['created_by'] = $user_id;
        $input['updated_by'] = $user_id;
        $input['status'] = '1';
        $propertyOwner = Employee::create($input);
        return response()->json([
        "success" => true,
        "message" => "Property Owners created successfully.",
        "data" => $propertyOwner
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PropertyOwner  $propertyOwner
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $propertyOwner = Employee::find($id);
        if (is_null($propertyOwner)) {
        return $this->sendError('Property Owners not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Property Owners retrieved successfully.",
        "data" => $propertyOwner
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PropertyOwner  $propertyOwner
     * @return \Illuminate\Http\Response
     */
    public function edit(PropertyOwner $propertyOwner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PropertyOwner  $propertyOwner
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        // 'name' => 'required',
        // 'contact' => 'required',
        // 'emirates_id' => 'required',
        // 'passport_number' => 'required',
        // 'nationality' => 'required',
        // 'email_address' => 'required',
        // 'emirates_id_file' => 'required|image|file|mimes:jpeg,png,jpg,gif',
        // 'passport' => 'required|image|file|mimes:jpeg,png,jpg,gif',
        // 'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }

        //Emirates ID upload
        if ($emirates_id_file = $request->file('emirates_id_file')) {
            $destinationPath = 'images/propertyOwner/emirates_id_file';
            $emirates_id_Image = date('YmdHis') . "." . $emirates_id_file->getClientOriginalExtension();
            $emirates_id_file->move($destinationPath, $emirates_id_Image);
            $input['emirates_id_file'] = "$emirates_id_Image";
        }

        //Passport upload
        if ($passport = $request->file('passport')) {
            $destinationPath = 'images/propertyOwner/passport';
            $passportImage = date('YmdHis') . "." . $passport->getClientOriginalExtension();
            $passport->move($destinationPath, $passportImage);
            $input['passport'] = "$passportImage";
        }

        $propertyOwner = Employee::find($id);
        $propertyOwner->name = $input['name'];
        $propertyOwner->contact = $input['contact'];
        $propertyOwner->emirates_id = $input['emirates_id'];
        $propertyOwner->passport_number = $input['passport_number'];
        $propertyOwner->nationality = $input['nationality'];
        $propertyOwner->email_address = $input['email_address'];
        $propertyOwner->branch_id = '4';
        // $propertyOwner->emirates_id_file = $input['emirates_id_file'];
        // $propertyOwner->passport = $input['passport'];
        $propertyOwner->updated_by = $user_id;
        $propertyOwner->save();
        return response()->json([
        "success" => true,
        "message" => "Property Owners updated successfully.",
        "data" => $propertyOwner
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PropertyOwner  $propertyOwner
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $propertyOwner = Employee::find($id);
        $propertyOwner->delete();
        return response()->json([
        "success" => true,
        "message" => "Property Owners deleted successfully.",
        "data" => $propertyOwner
        ]);
    }
}
