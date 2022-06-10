<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\User;
use App\Models\Task;
use App\Models\BranchUsers;
use App\Models\RoleUser;
use App\Models\AutomaticAssigning;
use App\Models\Company;
use App\Models\Branch;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
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
            //checking whether it is sales agent
            $tasks = Task::where('assigned_to_id',$id)->pluck('contact_id');
            if($tasks->isEmpty())
            {
                // Branch Admin
                $branch_id = BranchUsers::where('user_id',$id)->first();
                $contact = Contact::where('branch_id',$branch_id->branch_id)->whereHas('deals')->with(['deals' => function($q){
                    $q->with('leadstatus','users');
                }])->get();
            }
            else
            {
                // Sales Agent
                $branch_id = BranchUsers::where('user_id',$id)->first();
                $contact = Contact::where('branch_id',$branch_id->branch_id)->whereIn('id',$tasks)->whereHas('agentDeals')->with(['agentDeals' => function($q) {
                    $q->with('leadstatus','users');
                }])->get();

            }
       }
       else
       {
           // Company Super Admin
           $branch_id = Branch::where('company_id',$companyId->id)->pluck('id');
           $contact = Contact::whereHas('deals')->with(['deals' => function($q){
            $q->with('leadstatus');
            }])->get();
       }
        return response()->json([
        "success" => true,
        "message" => "Customers List",
        "data" => $contact,
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
    //     // dd($request->email);
    //     // dd($request->landing_page_link);
    //     $input = $request->all();
    //     $validator = Validator::make($input, [
    //     // 'first_name' => 'required',
    //     // 'email' => 'required',
    //     // 'phone_number' => 'required',
    //     // 'landing_page_link' => 'required',
    //     // 'street_address' => 'required',
    //     ]);
    //     if($validator->fails()){
    //     return $this->sendError('Validation Error.', $validator->errors());       
    //     }
        
    //     //no of repeats
    //     $no_of_repeats = Contact::where('email',$request->email)->count();
    //     // dd($no_of_repeats);
    //     $input['no_of_repeats'] = $no_of_repeats;
    //     $customer = Contact::create($input);
    //     $input['contact_id'] = $customer->id;
    //     $input['lead_status_id'] = '1';
    //     $tasks = Task::create($input);

    //     // find the admin for current branch
    //     $branchUsers = BranchUsers::where('branch_id',$request->branch_id)->pluck('user_id');
    //     $roleUsers = RoleUser::whereIn('user_id',$branchUsers)->where('role_id','3')->first();
    //     $userId = $roleUsers->user_id;



    //     //check whether autoassigning is active or inactive for this admin
    //     $autoAssign = AutomaticAssigning::where('admin_id',$userId)->first();
    //     if(!$autoAssign == null)
    //     {
    //         if($autoAssign->status == 'active')
    //         {
    //             //find all salesagent of current branch
    //             //branch_id = 2
    //             $branchUsers = BranchUsers::where('branch_id',$request->branch_id)->pluck('user_id');
    //             $roleUsers = RoleUser::whereIn('user_id',$branchUsers)->where('role_id','4')->pluck('user_id');
    //             $userTasks = User::whereIn('id',$roleUsers)->withCount('tasks')->orderBy('tasks_count', 'asc')->first();
    //             $input['branch_id'] = $request->branch_id;
    //             $input['contact_id'] = $customer->id;
    //             $input['lead_status_id'] = '1';
    //             $input['assigned_to_id'] = $userTasks->id;
    //             $input['created_by'] = $userId;
    //             $input['updated_by'] = $userId;
    //             $input['assigned_by_id'] = $userId;
               
    //             $task = Task::create($input);             
    //         }
    //     }
    

    //     $customer = Contact::find($customer->id);
    //     return response()->json([
    //     "success" => true,
    //     "message" => "Customer created successfully.",
    //     "data" => $customer
    //     ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        $contact = Contact::with('leadstatus','users','projects')->find($id);
        // dd($contact);
        if (is_null($contact)) {
        return $this->sendError('Customer not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Customer retrieved successfully.",
        "data" => $contact
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        // $input = $request->all();
        // $validator = Validator::make($input, [
        //     'name' => 'required',
        //     'email' => 'required',
        //     'phone' => 'required',
        //     'landing_page_link' => 'required',
        // ]);
        // if($validator->fails()){
        // return $this->sendError('Validation Error.', $validator->errors());       
        // }
        // $customer->name = $input['name'];
        // $customer->email = $input['email'];
        // $customer->phone = $input['phone'];
        // $customer->landing_page_link = $input['landing_page_link'];
        // $customer->save();
        // return response()->json([
        // "success" => true,
        // "message" => "Customer updated successfully.",
        // "data" => $customer
        // ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $customer = Customer::find($request->id);
        $customer->delete();
        return response()->json([
        "success" => true,
        "message" => "Customer deleted successfully.",
        "data" => $customer
        ]);
        }   
}
