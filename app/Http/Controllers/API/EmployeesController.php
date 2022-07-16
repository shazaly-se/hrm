<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeInfo;
use App\Models\EmployeeDetail;
use App\Models\Designation;
use App\Models\User;
use App\Models\BranchUsers;
use App\Models\RoleUser;
use App\Models\Role;
use App\Models\Company;
use App\Models\CompanyInfo;
use App\Models\Task;
use App\Models\Branch;
use App\Models\User_Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
use Illuminate\Support\Facades\Auth;

class EmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     
    
    public function index(/*$flag*/)
    {

         $employee = EmployeeInfo::leftJoin("designations","designations.id","=","employeeinfos.designationselected")
         ->get(array("employeeinfos.*","designations.designationName"));
         $designations = Designation::all();
         return response()->json([
            "success" => true,
            "employees" => $employee,
            "designations"=>$designations
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
    public function store(Request $request /*$flag*/)
    {
        
       // remove or edit image from public folder  unlink('uploads/docs/p-1650544384.jpeg');
        //return "jgkj";
        $company = CompanyInfo::first();
        $employee = new EmployeeInfo;
        $employeedetails = new EmployeeDetail;
        $user_role = new User_Role;
        $user = User::create([
    		'name' => $request->fullname,
    		'email' => $request->employeeEmail,
    		'password' => \Hash::make("123456789"),
        ]);
        if($user){
            $user_role->model_id = $user->id;
            $user_role->role_id = $request->role_id;
            $user_role->save();

            $employee->user_id = $user->id;
            $employee->company_id = $company->id;
            $employee->fullname = $request->fullname;
            $employee->birthdate = $request->birthdate;
            $employee->joineddate = $request->joineddate;
            $employee->gender = $request->gender;
            $employee->departmentselected = $request->departmentselected;
            $employee->designationselected = $request->designationselected;
            $employee->employeephone = $request->employeephone;
            $employee->employeeEmail = $request->employeeEmail;
            $employee->employeeAddress = $request->employeeAddress;
            // $employee->labourContractExpiry = $request->labourContractExpiry;
            $employee->contractStartDate = $request->contractStartDate;
            $employee->contractExpireDate = $request->contractExpireDate;
            $employee->visaExpiry = $request->visaExpiry;

            $employee->passportno = $request->passportno;
            $employee->passportExpiryDate = $request->passportExpiryDate;
            $employee->nationalityselected = $request->nationalityselected;
            $employee->religion = $request->religion;
            $employee->maritialStatus = $request->maritialStatus;
            $employee->numberofChildren = $request->numberofChildren;
            $employee->emergencycontactname = $request->emergencycontactname;
            $employee->emergencycontactrelationship = $request->emergencycontactrelationship;
            $employee->emergencycontactphone = $request->emergencycontactphone;
            $employee->employeebankname = $request->employeebankname;
            $employee->bankbranchname = $request->bankbranchname;
            $employee->employeebankacc = $request->employeebankacc;
            $employee->employeebankiban = $request->employeebankiban;
            $employee->employeehomeadd = $request->employeehomeadd;
            $employee->employeehomephone = $request->employeehomephone;
            $employee->employeehomephone2 = $request->employeehomephone2;

            if($request->get('imageUpload'))
            {
               $image = $request->get('imageUpload');
               $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
               \Image::make($request->get('imageUpload'))
               ->resize(292, 220, function ($constraint) {
                  $constraint->aspectRatio();
              })->save(public_path('uploads/profiles/').$name);
               $employee->image=$name;
             }
          

            if($employee->save()){

        
            $employeedetails->employee_id = $employee->id;
            if($request->input('passport_copy'))
            {
               $base64_image = $request->input('passport_copy'); // your base64 encoded     
               @list($type, $file_data) = explode(';', $base64_image);
               @list(, $file_data) = explode(',', $file_data); 
               $passportimageName = 'p-'.time().'.' . explode('/', explode(':', substr($base64_image, 0, strpos($base64_image, ';')))[1])[1];  
               Storage::disk('public')->put($passportimageName, base64_decode($file_data));
               $employeedetails->passport_copy=$passportimageName;
    
            }
              if($request->input('certification_copy'))
              {
                $base64_image = $request->input('certification_copy'); // your base64 encoded     
                @list($type, $file_data) = explode(';', $base64_image);
                @list(, $file_data) = explode(',', $file_data);
                 $certificationimagename = 'c-'.time().'.' . explode('/', explode(':', substr($base64_image, 0, strpos($base64_image, ';')))[1])[1];  
                 Storage::disk('public')->put($certificationimagename, base64_decode($file_data));
                 $employeedetails->certification_copy=$certificationimagename;
               }
               if($request->input('id_copy'))
               {
                $base64_image = $request->input('id_copy'); // your base64 encoded     
                @list($type, $file_data) = explode(';', $base64_image);
                @list(, $file_data) = explode(',', $file_data);
                 $idcopyimagename = 'id-'.time().'.' . explode('/', explode(':', substr($base64_image, 0, strpos($base64_image, ';')))[1])[1];  
                 Storage::disk('public')->put($idcopyimagename, base64_decode($file_data));
                 $employeedetails->id_copy=$idcopyimagename;
                }
               
                $employeedetails->basic_salary = $request->basic_salary;
                $employeedetails->transport_allowance = $request->transport_allowance;
                $employeedetails->house_allowance = $request->house_allowance;
                $employeedetails->other_allowance = $request->other_allowance;
                $employeedetails->total_salary = $request->basic_salary + $request->transport_allowance + $request->house_allowance + $request->other_allowance;
                $employeedetails->save();
            
            return response()->json([
                "success" => true,
                "message" => "data created successfully.",
                "data" => $employee
                ]);
        }
    }

       
        //return $request->all();
        // $id = Auth::id();
        // $input = $request->all();
        // $validator = Validator::make($input, [
        // // 'name' => 'required',
        // // 'contact' => 'required',
        // // 'emirates_id' => 'required',
        // // 'passport_number' => 'required',
        // // 'nationality' => 'required',
        // // 'email_address' => 'required',
        // // 'branch_id' => 'required',

        // // 'joining_date' => 'required',
        // // 'visa_number' => 'required',
        // // 'visa_expiry_date' => 'required',
        // // 'labour_contract_number' => 'required',
        // // 'labour_contract_expiry_date' => 'required',
        // // 'visa' => 'required|image|file|mimes:jpeg,png,jpg,gif',
        // // 'labour_contract' => 'required|image|file|mimes:jpeg,png,jpg,gif',
        // // 'photo' => 'required|image|file|mimes:jpeg,png,jpg,gif',
        // // 'passport' => 'required|image|file|mimes:jpeg,png,jpg,gif',
        // // 'emirates_id_file' => 'required|image|file|mimes:jpeg,png,jpg,gif',
        // ]);
        // if($validator->fails()){
        // return $this->sendError('Validation Error.', $validator->errors());       
        // }

        //Emirates ID upload
        // if ($emirates_id_file = $request->file('emirates_id_file')) {
        //     $destinationPath = 'images/Employees/emirates_id_file';
        //     $emirates_id_Image = date('YmdHis') . "." . $emirates_id_file->getClientOriginalExtension();
        //     $emirates_id_file->move($destinationPath, $emirates_id_Image);
        //     $input['emirates_id_file'] = "$emirates_id_Image";
        // }

        // //Passport upload
        // if ($passport = $request->file('passport')) {
        //     $destinationPath = 'images/Employees/passport';
        //     $passportImage = date('YmdHis') . "." . $passport->getClientOriginalExtension();
        //     $passport->move($destinationPath, $passportImage);
        //     $input['passport'] = "$passportImage";
        // }
        // //Photo upload
        // if ($photo = $request->file('photo')) {
        //     $destinationPath = 'images/Employees/photo';
        //     $photoImage = date('YmdHis') . "." . $photo->getClientOriginalExtension();
        //     $photo->move($destinationPath, $photoImage);
        //     $input['photo'] = "$photoImage";
        // }

        // //Labour Contract upload
        // if ($labour_contract = $request->file('labour_contract')) {
        //     $destinationPath = 'images/Employees/labour_contract';
        //     $labourContractImage = date('YmdHis') . "." . $labour_contract->getClientOriginalExtension();
        //     $labour_contract->move($destinationPath, $labourContractImage);
        //     $input['labour_contract'] = "$labourContractImage";
        // }
        // //Visa upload
        // if ($visa = $request->file('visa')) {
        //     $destinationPath = 'images/Employees/visa';
        //     $visaImage = date('YmdHis') . "." . $visa->getClientOriginalExtension();
        //     $visa->move($destinationPath, $visaImage);
        //     $input['visa'] = "$visaImage";
        // }

        // $input['status'] = $flag;
        // //checking if branchid null
        // if($request->branch_id == null)
        // {
        //     $branch = BranchUsers::where('user_id',$id)->first();
        //     $input['branch_id'] = $branch->branch_id;
        // }
        // if($flag == 0)
        // {
        //     if(!$request->user_id)
        //     {
        //         $user = User::create([
        //             'name' => $request->name,
        //             'email' => $request->email,
        //             'password' => bcrypt($request->password),
        //         ]);
        //     }
        //    else
        //    {
        //         $user = User::find($request->user_id);
        //    }
        //     $input['user_id'] = $user->id;
        //     $input['created_by'] = $id;
        //     $input['updated_by'] = $id;
        //     $branchUser = BranchUsers::create($input);
        //     $roleUser = RoleUser::create($input);
        // }


       // $employee = EmployeeInfo::create($request->all());
   
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show( $id)
    {
       //return "jgkg";
        $employee = EmployeeInfo::leftJoin("employee_details","employee_details.employee_id","=","employeeinfos.id")
                                ->leftJoin("model_has_roles","model_has_roles.model_id","=","employeeinfos.user_id")
                                ->leftJoin("roles","roles.id","=","model_has_roles.role_id")
                                ->leftJoin("designations","designations.id","=","employeeinfos.designationselected")
                                ->leftJoin("departments","departments.id","=","employeeinfos.departmentselected")
                                ->where("employeeinfos.id",$id)
                                ->first(array(
                                    "employeeinfos.*","departments.department","designations.designationName",
                                   // "employee_details.passport_copy","employee_details.certification_copy","employee_details.id_copy",
                                    "employee_details.basic_salary","employee_details.transport_allowance","employee_details.house_allowance",
                                    "employee_details.other_allowance","model_has_roles.role_id","roles.name as roleName","employee_details.passport_copy as savedpassport_copy",
                                    "employee_details.id_copy as savedid_copy","employee_details.certification_copy as savedcertification_copy"));
        if (!$employee) {
        return 'Employee not found.';
        }else{
            
            return response()->json([
                "success" => true,
                "message" => "data retrieved successfully.",
                "employee" => $employee
                ]);
        }
     
    }

    public function update(Request $request,$id)
    {
        //return $request->all();
     //return  Storage::delete($request->input('savedid_copy'));
        $employee =  EmployeeInfo::find($id);
        //return $employee;
        $employeedetails =  EmployeeDetail::where("employee_id",$id)->first();
    
        $user_role =  User_Role::where("model_id",$employee->user_id)->first();
        if(!$employee){
            return 'Employee not found.';  
        }
        $employee->fullname = $request->fullname;
        $employee->birthdate = $request->birthdate;
        $employee->joineddate = $request->joineddate;
        $employee->gender = $request->gender;
        $employee->departmentselected = $request->departmentselected;
        $employee->designationselected = $request->designationselected;
        $employee->employeephone = $request->employeephone;
        $employee->employeeEmail = $request->employeeEmail;
        $employee->employeeAddress = $request->employeeAddress;
       // $employee->labourContractExpiry = $request->labourContractExpiry;
        $employee->contractStartDate = $request->contractStartDate;
        $employee->contractExpireDate = $request->contractExpireDate;
        $employee->visaExpiry = $request->visaExpiry;
        $employee->passportno = $request->passportno;
        $employee->passportExpiryDate = $request->passportExpiryDate;
        $employee->nationalityselected = $request->nationalityselected;
        $employee->religion = $request->religion;
        $employee->maritialStatus = $request->maritialStatus;
        $employee->numberofChildren = $request->numberofChildren;
        $employee->emergencycontactname = $request->emergencycontactname;
        $employee->emergencycontactrelationship = $request->emergencycontactrelationship;
        $employee->emergencycontactphone = $request->emergencycontactphone;
        $employee->employeebankname = $request->employeebankname;
        $employee->bankbranchname = $request->bankbranchname;
        $employee->employeebankacc = $request->employeebankacc;
        $employee->employeebankiban = $request->employeebankiban;
        $employee->employeehomeadd = $request->employeehomeadd;
        $employee->employeehomephone = $request->employeehomephone;
        $employee->employeehomephone2 = $request->employeehomephone2;
        if($request->get('imageUpload'))
        {
           $image = $request->get('imageUpload');
           $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
           \Image::make($request->get('imageUpload'))
           ->resize(292, 220, function ($constraint) {
              $constraint->aspectRatio();
          })->save(public_path('uploads/profiles/').$name);
           $employee->image=$name;
         }
        
        if($employee->update()){

           
            $user_role->role_id = $request->role_id;
            $user_role->update();

            //uploads
            if($request->input('passport_copy'))
            {
                unlink(storage_path('app/public/'.$request->input('savedpassport_copy')));
         
               $base64_image = $request->input('passport_copy'); // your base64 encoded     
               @list($type, $file_data) = explode(';', $base64_image);
               @list(, $file_data) = explode(',', $file_data); 
               $passportimageName = 'p-'.time().'.' . explode('/', explode(':', substr($base64_image, 0, strpos($base64_image, ';')))[1])[1];  
               Storage::disk('public')->put($passportimageName, base64_decode($file_data));
               $employeedetails->passport_copy=$passportimageName;
    
            }
              if($request->input('certification_copy'))
              {
                  
                unlink(storage_path('app/public/'.$request->input('savedcertification_copy')));
        
                $base64_image = $request->input('certification_copy'); // your base64 encoded     
                @list($type, $file_data) = explode(';', $base64_image);
                @list(, $file_data) = explode(',', $file_data);
                 $certificationimagename = 'c-'.time().'.' . explode('/', explode(':', substr($base64_image, 0, strpos($base64_image, ';')))[1])[1];  
                 Storage::disk('public')->put($certificationimagename, base64_decode($file_data));
                 $employeedetails->certification_copy=$certificationimagename;
               }
               if($request->input('id_copy'))
               {
            
                unlink(storage_path('app/public/'.$request->input('savedid_copy')));
                $base64_image = $request->input('id_copy'); // your base64 encoded     
                @list($type, $file_data) = explode(';', $base64_image);
                @list(, $file_data) = explode(',', $file_data);
                 $idcopyimagename = 'id-'.time().'.' . explode('/', explode(':', substr($base64_image, 0, strpos($base64_image, ';')))[1])[1];  
                 Storage::disk('public')->put($idcopyimagename, base64_decode($file_data));
                 $employeedetails->id_copy=$idcopyimagename;
                }
                $employeedetails->basic_salary = $request->basic_salary;
                $employeedetails->transport_allowance = $request->transport_allowance;
                $employeedetails->house_allowance = $request->house_allowance;
                $employeedetails->other_allowance = $request->other_allowance;
                $employeedetails->total_salary = $request->basic_salary + $request->transport_allowance + $request->house_allowance + $request->other_allowance;
                $employeedetails->update();
     
     return response()->json([
         "success" => true,
         "message" => "data created successfully.",
         "data" => $employee
         ]);
 }
       
  
        
    }

    public function search(Request $request)
    {
        
        $employee = EmployeeInfo::leftJoin("designations","designations.id","=","employeeinfos.designationselected")
        ->where("fullname",'LIKE', "%".$request->searchedName."%")
        ->where(function ($query) use($request){
            if($request->searchedDesignation > 0) {$query->where("designationselected",$request->searchedDesignation);}
            if($request->searchedID > 0) {$query->where("employeeinfos.id",$request->searchedID);}
        })
 
         ->get(array("employeeinfos.*","designations.designationName"));
     
        return response()->json([
            "success" => true,
            "employees" => $employee
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
  

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
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
        $employee = Employee::find($id);
        // dd($input['name']);
        // dd($employee);
        // $employee = $input;
        // $employee->save();
        // dd('hi');
        $employee->name = $input['name'];
        $employee->contact = $input['contact'];
        $employee->emirates_id = $input['emirates_id'];
        $employee->passport_number = $input['passport_number'];
        $employee->nationality = $input['nationality'];
        $employee->email_address = $input['email_address'];
        $employee->branch_id = $input['branch_id'];

        $employee->joining_date = $input['joining_date'];
        $employee->visa_number = $input['visa_number'];
        $employee->visa_expiry_date = $input['visa_expiry_date'];
        $employee->labour_contract_number = $input['labour_contract_number'];
        $employee->labour_contract_expiry_date = $input['labour_contract_expiry_date'];
        $employee->visa = $input['visa'];
        $employee->labour_contract = $input['labour_contract'];
        $employee->photo = $input['photo'];
        $employee->passport = $input['passport'];
        $employee->emirates_id_file = $input['emirates_id_file'];
        $employee->status = $input['status'];
        $employee->updated_by = $user_id;
        $employee->save();
        return response()->json([
        "success" => true,
        "message" => "Data updated successfully.",
        "data" => $employee
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //return $id;
        //$user_id = Auth::id();
        $employee = EmployeeInfo::find($id);
        $employee->delete();
       // $employee->deleted_by = $user_id;
       // $employee->save();
        return response()->json([
        "success" => true,
        "message" => "Employee deleted successfully.",
        "data" => $employee
        ]);
    }
}
