<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Branch;
use App\Models\BranchUsers;
use App\Models\User;
use App\Models\Role;
use Validator;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;


class CompanyController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = Auth::id();

        // $branch_users = BranchUsers::where('user_id',$id)->pluck('branch_id');
        // $branches = Branch::whereIn('id', $branch_users)->with('company')->get();
        // $companies = Company::where('created_by', $id)->with('branches')->get();
        $companies = Company::all();
        if($companies->isEmpty())
        {
            return response()->json([
                "success" => false,
                "message" => "You dont have any company, create new company.",
                ]); 
        }
        $users = User::all();
        return response()->json([
        "success" => true,
        "message" => "My Company List",
        "data" => ['companies'=>$companies], ['users'=>$users]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        // $user = Auth::user(); // Retrieve the currently authenticated user...
        // $id = Auth::id(); // Retrieve the currently authenticated user's ID...

        // $user = $request->user(); // returns an instance of the authenticated user...
        // $id = $request->user()->id; // Retrieve the currently authenticated user's ID...
        // return response()->json([
        //     "success" => true,
        //     "message" => "Get auth successfully.",
        //     "data" => $id
        //     ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        // try
        // {  
            $user = Auth::user();
            $id = Auth::id();
            $input = $request->all();
            $validator = Validator::make($input, [
            // 'name' => 'required',
            // 'branch_name' => 'required',
            // 'address' => 'required',
            // 'zipcode' => 'required',
            // 'phone' => 'required',
            // 'email' => 'required',
            // 'logo' => 'required|image|file|mimes:jpeg,png,jpg,gif',
            ]);
            if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
            }
            //logo upload
        if ($logo = $request->file('logo')) {
            $destinationPath = 'images/company/logo';
            $logoImage = date('YmdHis') . "." . $logo->getClientOriginalExtension();
            $logo->move($destinationPath, $logoImage);
            $input['logo'] = "$logoImage";
        }

        // if($request->file('logo'))
        //  {
        //     $image = $request->file('logo');
        //     $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            
        //     \Image::make($request->file('logo'))->save(public_path('images/company/logo/').$name);
        //     $input['logo'] =$name;
        //   }

            // $company = Company::where('created_by',$id)->first();
            // dd($company);
            // if($company == null)
            // {
                $input['password'] = \Hash::make($request->password);
                $user = User::create($input);
                $input['user_id'] = $user->id;
                $input['created_by'] = $id;
                $input['updated_by'] = $id;
                $company = Company::create($input);

                $input['company_id'] = $company->id;
                $branch = Branch::create($input);
               
                // $input['branch_id'] = $branch->id;
                // $input['user_id'] = $id;
                // $branchUsers = BranchUsers::create($input);
                $input['name'] = 'SuperAdmin';
                $role = Role::create($input);
                // $input['role_id'] = $role->id;
                // $userRole = UserRoles::create($input);
                return response()->json([
                "success" => true,
                "message" => "Company created successfully.",
                "data" => $company
                ]);
            // }
            // else
            // {
            //     return response()->json([
            //         "success" => false,
            //         "message" => "User Already Have Company.",
            //         "data" => $company
            //         ]);
            // }
        // }
        // catch (\Exception $e) 
        // {
        // throw new HttpException(500, $e->getMessage());
        // return $e;
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = Company::with('branches')->get()->find($id);
        // $company = Company::find($id)->with('branches')->get();
        // ->with('branches')->get();
        if (is_null($company)) {
        return $this->sendError('Company not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Company retrieved successfully.",
        "data" => $company
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
            // 'name' => 'required',
            // 'email' => 'required',
            // 'logo' => 'required|image|file|mimes:jpeg,png,jpg,gif',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        //logo upload
        if ($logo = $request->file('logo')) {
            $destinationPath = 'images/company/logo';
            $logoImage = date('YmdHis') . "." . $logo->getClientOriginalExtension();
            $logo->move($destinationPath, $logoImage);
            $input['logo'] = "$logoImage";
        }
        $company = Company::find($id);
        $company->name = $input['name'];
        $company->email = $input['email'];
        // $company->email = $input['logo'];
        $company->updated_by = $user_id;
        $company->save();
         
        // $company = Company::find($id);
        // $company->name = $input['name'];
        // $company->updated_by = $user_id;
        // $company->save();
            return response()->json([
                "success" => true,
                "message" => "Company updated successfully.",
                "data" => $company
                ]);           
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $company = Company::find($id);
        $company->delete();
        return response()->json([
        "success" => true,
        "message" => "Company deleted successfully.",
        "data" => $company
        ]);
    }
    public function filterByInputs(Request $request)
    {     
        $name = $request->name;
        $email = $request->email;
        $created_by = $request->created_by;
        $start_date = $request->start_date;      
        $end_date = $request->end_date;
        
       
        $companies = Company::query();
            if($name)
            {
                $companies = $companies->where('name',$name);
            }
            if($email)
            {
                $companies = $companies->where('email',$email);
            }
            if($created_by)
            {
                $companies = $companies->where('created_by',$created_by);
            }
            if($start_date)
            {
                $start_date = $start_date.' 00:00:00';
                $companies = $companies->where('created_at','>=',$start_date);
            }
            if($end_date)
            {
                $end_date = $end_date.' 23:59:59';
                $companies = $companies->where('created_at','<=',$end_date);
            }
        $companies = $companies->get();
        return response()->json([
            "success" => true,
            "message" => "Companies filterd successfully.",
            "data" => $companies
            ]);
    }
}
