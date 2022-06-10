<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BranchUsers;
use App\Models\Company;
use App\Models\Branch;
use App\Models\User;
use App\Models\RoleUser;
use App\Models\Role;
use App\Models\Employee;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BranchUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = Auth::id();
// checking whether it is superadmin
        $companyId = Company::where('user_id',$id)->first();
        if($companyId == NULL)
        {
            // dd($id);
            $branch = BranchUsers::where('user_id',$id)->first();
            $branch_id = $branch->branch_id;
            // dd($branch_id);
            $branchUsers = BranchUsers::with('branches')->where('branch_id', $branch_id)->pluck('user_id');
            $branchUsers = User::with('branches','roles')->whereIn('id', $branchUsers)->where('status','active')->get();
            $companyId = Branch::find($branch_id);
            $companyId = $companyId->id;
            // dd($companyId);
            $roles = Role::where('company_id',$companyId)->get();
            // dd($roles);
        }
        else{
            $branch_id = Branch::where('company_id',$companyId->id)->pluck('id');
            $branchUsers = BranchUsers::whereIn('branch_id', $branch_id)->get();
          
            $branchUsers = BranchUsers::with('branches')->whereIn('branch_id', $branch_id)->pluck('user_id');
            $branchUsers = User::with('branches','roles')->whereIn('id', $branchUsers)->where('status','active')->get();
            // dd($branchUsers);
        }
       

    
        // dd($branchUsers);
        return response()->json([
        "success" => true,
        "message" => "Branch Users List",
        "data" => $branchUsers
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
//     public function store(Request $request)
//     {
//         $id = Auth::id();
//         $input = $request->all();
//         $validator = Validator::make($input, [
//         // 'branch_id' => 'required',
//         // 'name' => 'required',
//         // 'email' => 'required',
//         // 'password' => 'required',
//         ]);
//         if($validator->fails()){
//         return $this->sendError('Validation Error.', $validator->errors());       
//         }
//         // check whether its existing user or new user
//         if(!$request->user_id)
//         {
//             $user = User::create([
//                 'name' => $request->name,
//                 'email' => $request->email,
//                 'password' => bcrypt($request->password),
//             ]);
//         }
//        else
//        {
//             $user = User::find($request->user_id);
//        }
//         $input['user_id'] = $user->id;
//         $input['created_by'] = $id;
//         $input['updated_by'] = $id;
//         // $input['role_id'] = '4';
// //checking if branchid null
// if($request->branch_id == null)
// {
//     $branch = BranchUsers::where('user_id',$id)->first();
//     $input['branch_id'] = $branch->branch_id;
// }
//     $branchUser = BranchUsers::create($input);
//     $roleUser = RoleUser::create($input);

//         return response()->json([
//         "success" => true,
//         "message" => "Branch Users created successfully.",
//         "user" => $user,
//         "branchUser" => $branchUser,
//         "roleUser" => $roleUser,
//         ]);
//     }
public function store(Request $request, $flag)
    {
        $id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        // 'name' => 'required',
        // 'contact' => 'required',
        // 'emirates_id' => 'required',
        // 'passport_number' => 'required',
        // 'nationality' => 'required',
        // 'email_address' => 'required',
        // 'branch_id' => 'required',

        // 'joining_date' => 'required',
        // 'visa_number' => 'required',
        // 'visa_expiry_date' => 'required',
        // 'labour_contract_number' => 'required',
        // 'labour_contract_expiry_date' => 'required',
        // 'visa' => 'required|image|file|mimes:jpeg,png,jpg,gif',
        // 'labour_contract' => 'required|image|file|mimes:jpeg,png,jpg,gif',
        // 'photo' => 'required|image|file|mimes:jpeg,png,jpg,gif',
        // 'passport' => 'required|image|file|mimes:jpeg,png,jpg,gif',
        // 'emirates_id_file' => 'required|image|file|mimes:jpeg,png,jpg,gif',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }

        //Emirates ID upload
        if ($emirates_id_file = $request->file('emirates_id_file')) {
            $destinationPath = 'images/Employees/emirates_id_file';
            $emirates_id_Image = date('YmdHis') . "." . $emirates_id_file->getClientOriginalExtension();
            $emirates_id_file->move($destinationPath, $emirates_id_Image);
            $input['emirates_id_file'] = "$emirates_id_Image";
        }

        //Passport upload
        if ($passport = $request->file('passport')) {
            $destinationPath = 'images/Employees/passport';
            $passportImage = date('YmdHis') . "." . $passport->getClientOriginalExtension();
            $passport->move($destinationPath, $passportImage);
            $input['passport'] = "$passportImage";
        }
        //Photo upload
        if ($photo = $request->file('photo')) {
            $destinationPath = 'images/Employees/photo';
            $photoImage = date('YmdHis') . "." . $photo->getClientOriginalExtension();
            $photo->move($destinationPath, $photoImage);
            $input['photo'] = "$photoImage";
        }

        //Labour Contract upload
        if ($labour_contract = $request->file('labour_contract')) {
            $destinationPath = 'images/Employees/labour_contract';
            $labourContractImage = date('YmdHis') . "." . $labour_contract->getClientOriginalExtension();
            $labour_contract->move($destinationPath, $labourContractImage);
            $input['labour_contract'] = "$labourContractImage";
        }
        //Visa upload
        if ($visa = $request->file('visa')) {
            $destinationPath = 'images/Employees/visa';
            $visaImage = date('YmdHis') . "." . $visa->getClientOriginalExtension();
            $visa->move($destinationPath, $visaImage);
            $input['visa'] = "$visaImage";
        }

        $input['status'] = $flag;
        //checking if branchid null
        if($request->branch_id == null)
        {
            $branch = BranchUsers::where('user_id',$id)->first();
            $input['branch_id'] = $branch->branch_id;
        }
        if($flag == 0)
        {
            if(!$request->user_id)
            {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                ]);
            }
           else
           {
                $user = User::find($request->user_id);
           }
            $input['user_id'] = $user->id;
            $input['created_by'] = $id;
            $input['updated_by'] = $id;
            $branchUser = BranchUsers::create($input);
            $roleUser = RoleUser::create($input);
        }

        $employee = Employee::create($input);
        return response()->json([
        "success" => true,
        "message" => "data created successfully.",
        "data" => $employee
        ]);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BranchUsers  $branchUsers
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with('branches','roles')->find($id);
        // dd($user);
        // $branchUsers = BranchUsers::find($id);
        // if (is_null($branchUsers)) {
        // return $this->sendError('Company not found.');
        // }
        return response()->json([
        "success" => true,
        "message" => "Branch Users retrieved successfully.",
        "data" => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BranchUsers  $branchUsers
     * @return \Illuminate\Http\Response
     */
    public function edit(BranchUsers $branchUsers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BranchUsers  $branchUsers
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        // 'user_id' => 'required',
        // 'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $user = User::find($id);
        $user->name = $input['name'];
        $user->email = $input['email'];       
        $user->save();
        $branchUser = BranchUsers::where('user_id',$id)->first();
        $branchUser->branch_id = $input['branch_id'];   
        $branchUser->updated_by = $user_id;
        $user = User::with('branches','roles')->find($id);
        return response()->json([
        "success" => true,
        "message" => "Branch User updated successfully.",
        "data" => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BranchUsers  $branchUsers
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->status = 'inactive';
        $user->save();
        $user = User::find($id);
        // $branchUser = BranchUsers::where('user_id', $id);
        // $branchUser->delete();
        // $roleUser = RoleUser::where('user_id', $id);
        // $roleUser->delete();
        return response()->json([
        "success" => true,
        "message" => "Branch Users inactive successfully.",
        "data" => $user,
        ]);
    }
    public function filterByInputs(Request $request)
    {
        // dd($request->all());
        $user_id = $request->user_id;
        $branch_id = $request->branch_id;
        
        $branchUsers = BranchUsers::query();
            if($user_id)
            {
                $branchUsers = $branchUsers->where('user_id',$user_id);
            }
            if($branch_id)
            {
                $branchUsers = $branchUsers->where('branch_id',$branch_id);
            }
            
        $branchUsers = $branchUsers->get();

        return response()->json([
            "success" => true,
            "message" => "Branch Users filterd successfully.",
            "data" => $branchUsers
            ]);
    }
}
