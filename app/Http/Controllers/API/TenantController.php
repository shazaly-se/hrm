<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Employee;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tenant = Employee::where('status','2')->get();
        return response()->json([
        "success" => true,
        "message" => "Tenant List",
        "data" => $tenant
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
        $input['status'] = '2';
        $tenant = Employee::create($input);
        return response()->json([
        "success" => true,
        "message" => "Tenant created successfully.",
        "data" => $tenant
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tenant = Employee::find($id);
        if (is_null($tenant)) {
        return $this->sendError('Tenant not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Tenant retrieved successfully.",
        "data" => $tenant
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\Response
     */
    public function edit(Tenant $tenant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tenant  $tenant
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

        $tenant = Employee::find($id);
        $tenant->name = $input['name'];
        $tenant->contact = $input['contact'];
        $tenant->emirates_id = $input['emirates_id'];
        $tenant->passport_number = $input['passport_number'];
        $tenant->nationality = $input['nationality'];
        $tenant->email_address = $input['email_address'];
        $tenant->emirates_id_file = $input['emirates_id_file'];
        $tenant->passport = $input['passport'];
        $tenant->branch_id = $input['branch_id'];
        $tenant->updated_by = $user_id;
        $tenant->save();
        return response()->json([
        "success" => true,
        "message" => "Tenant updated successfully.",
        "data" => $tenant
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $tenant = Employee::find($request->id);
        $tenant->delete();
        return response()->json([
        "success" => true,
        "message" => "Tenant deleted successfully.",
        "data" => $tenant
        ]);
    }
}
