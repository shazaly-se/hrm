<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\BranchUsers;
use Validator;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd($request->all());
        // $branches = Branch::where('company_id', $company_id)->get();
        // $branches = Branch::all();
        // return response()->json([
        // "success" => true,
        // "message" => "Company Branches List",
        // "data" => $branches
        // ]);
        $id = Auth::id();
        
        // dd($id);
        // $branch_users = BranchUsers::where('user_id',$id)->pluck('branch_id');
        // $branches = Branch::where('id', $branch_users)->with('company')->get();
        $companies = Company::where('user_id', $id)->with('branches')->get();
        if($companies->isEmpty())
        {
            $branchUsers = BranchUsers::where('user_id',$id)->pluck('branch_id');
            
            $companies = Branch::find($branchUsers);
            // dd($companies);
            // $companies = BranchUsers::whereIn('branch_id', $branchUsers)->pluck('branch_id');
            // dd($companies);
            // $branchUsers = User::with('branches','roles')->whereIn('id', $branchUsers)->where('status','active')->get();
            return response()->json([
                "success" => false,
                "message" => "My Branch",
                "data" => $companies
                ]); 
        }
        return response()->json([
        "success" => true,
        "message" => "My Company List",
        "data" => $companies
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
            $company = Company::where('user_id',$id)->first();
            // dd($company);
            $input = $request->all();
            $validator = Validator::make($input, [
            'branch_name' => 'required',
            'address' => 'required',
            'zipcode' => 'required',
            'phone' => 'required',
            'email' => 'required',
            // 'company_id' => 'required',
            ]);
            if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
            }
            $input['created_by'] = $id;
            $input['updated_by'] = $id;
            $input['company_id'] = $company->id;
            $branch = Branch::create($input);
            return response()->json([
            "success" => true,
            "message" => "Branch created successfully.",
            "data" => $branch
            ]);
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
        $branch = Branch::where('id',$id)->get();
        if (is_null($branch)) {
        return $this->sendError('Branch not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Branch retrieved successfully.",
        "data" => $branch
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
            'branch_name' => 'required',
            'address' => 'required',
            'zipcode' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $branch = Branch::find($id);
        $branch->branch_name = $input['branch_name'];
        $branch->address = $input['address'];
        $branch->zipcode = $input['zipcode'];
        $branch->phone = $input['phone'];
        $branch->email = $input['email'];
        $branch->updated_by = $user_id;
        $branch->save();
        // $company = Company::find($request->company_id);
        // $company->name = $input['name'];
        // $company->updated_by = $user_id;
        // $company->save();
            return response()->json([
                "success" => true,
                "message" => "Branch updated successfully.",
                "data" => $branch
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
        
        $branch = Branch::find($id);
        $branch->delete();
        return response()->json([
        "success" => true,
        "message" => "Branch deleted successfully.",
        "data" => $branch
        ]);
    }

    public function filterByInputs(Request $request, $companyId)
    {
        $branch_name = $request->branch_name;
        $address = $request->address;
        $zipcode = $request->zipcode;
        $phone = $request->phone;
        $email = $request->email;
        $created_by = $request->created_by;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branches = Branch::where('company_id', $companyId);
            if($branch_name)
            {
                $branches = $branches->where('branch_name',$branch_name);
            }
            if($address)
            {
                $branches = $branches->where('address',$address);
            }
            if($zipcode)
            {
                $branches = $branches->where('zipcode',$zipcode);
            }
            if($phone)
            {
                $branches = $branches->where('phone',$phone);
            }
            if($email)
            {
                $branches = $branches->where('email',$email);
            }
            if($created_by)
            {
                $branches = $branches->where('created_by',$created_by);
            }
            if($start_date)
            {
                $start_date = $start_date.' 00:00:00';
                $branches = $branches->where('created_at','>=',$start_date);
            }
            if($end_date)
            {
                $end_date = $end_date.' 23:59:59';
                $branches = $branches->where('created_at','<=',$end_date);
            }

        $branches = $branches->get();

        return response()->json([
            "success" => true,
            "message" => "Branches filterd successfully.",
            "data" => $branches
            ]);
    }
}
