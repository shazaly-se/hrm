<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\EmployeeInfo;
use App\Models\DeviceID;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendUserController extends Controller
{
	public function login(Request $request)
    {
	

        
        $request->validate([
            'email' => 'required|email',
    		'password' => 'required',
			//'deviceId'=>'required'
        ]);

		
    	
		$credentials = [
			'email'=>$request->email,
			'password'=>$request->password
		];
		//return $credentials;

		$check_device = DeviceID::where("device_id",$request->deviceId)->first();
		//return $check_device;
		if($check_device){
			
			if (Auth::attempt($credentials)) {
			
				$user = Auth::user();
				$employee= EmployeeInfo::where("user_id",$user->id)->first();
				$role= Role::join("model_has_roles","model_has_roles.role_id","roles.id")
				->where("model_has_roles.model_id",$user->id)
				->first();
				if($user->id == $check_device->user_id){
				
					$token= $user->createToken('MyApp')->accessToken;
					$user["image"]=$employee->image;
					$user["fullname"]=$employee->fullname;
					$user["role_id"]=$role->id;
					$user["role_name"]=$role->name;

					return response()->json(['success' =>true,"msg"=>"old device","token"=> $token,"user"=>$user], 200);
				}else{
					return response()->json(['success' =>false,"msg"=> "This device registered for other user"]);
				}
			
			}
			else {
				return response()->json(['error' => 'Unauthorized'], 401);
			}
		}else{

			if (Auth::attempt($credentials)) {
			     	$newdevice= new DeviceID;
					
				    $user = Auth::user();
				    	$employee= EmployeeInfo::where("user_id",$user->id)->first();
						$role= Role::join("model_has_roles","model_has_roles.role_id","roles.id")
						->where("model_has_roles.model_id",$user->id)
						->first();
					$newdevice->user_id=$user->id;
					$newdevice->device_id = $request->deviceId;
					if($newdevice->save()){
					 $token= $user->createToken('MyApp')->accessToken;
					 $user["image"]=$employee->image;
			     	 $user["fullname"]=$employee->fullname;
					 $user["role_id"]=$role->id;
					 $user["role_name"]=$role->name;
						return response()->json(['success' =>true,"msg"=>"new device","token"=> $token,"user"=>$user], 200);
					}
					
			}else{
				return response()->json(['success' =>true,"msg"=> "This device registered for other user"]);
			}

		}
        
   
        
    	// if (Auth::attempt($credentials)) {
			
		// 	$user = Auth::user()
		// 	->join('employeeinfos','employeeinfos.user_id',"users.id")
		// 	->join('companyinfos','companyinfos.id',"employeeinfos.company_id")
		// 	->first(array("users.*","employeeinfos.image","companyinfos.ip_address"));
		// 	return $user;
		// 	//$employee=EmployeeInfo::where("user_id",$user->id)->first(array("image"));
		// 	$token= $user->createToken('MyApp')->accessToken;
		// 	return response()->json(['success' =>true,"token"=> $token,"user"=>$user], 200);
		// }
	
    }
   
    public function register(Request $request)
    {

    	$request->validate([
            'name' => 'required',
			'email' => 'required|email',
			'password' => 'required',
			'c_password' => 'required|same:password',
        ]);

    	$user = User::create([
    		'name' => $request->name,
    		'email' => $request->email,
    		'password' => \Hash::make($request->password),
        ]);
        
    	$success['name'] = $user->name;
    	$success['token'] = $user->createToken('MyApp')->accessToken;
    	return response()->json(['success' => $success], 200);
    }
   
	public function adminLogin(Request $request)
	{
		$request->validate([
            'email' => 'required|email',
    		'password' => 'required',
        ]);

        $credentials = $request->only(['email', 'password']);
        
		if (Auth::attempt($credentials)) {
			
			$user = Auth::user();
			if($user->status == 'inactive')
			{
				return response()->json(['success' => false,'error' => 'your account is inactive'], 401);
			}
			else{
				$userRoles = $user->roles->pluck('id');
				$token = $user->createToken('MyApp')->accessToken;
				return response()->json(['success' => true,'token' => $token, 'user' =>$user, 'userRoles' =>$userRoles], 200);
			}
			
		}
		else {
			return response()->json(['success' => false,'error' => 'Unauthorized'], 401);
		}
	}
	
	public function adminRegister(Request $request)
	{
		$request->validate([
            'name' => 'required',
			'email' => 'required|email',
			'password' => 'required',
			'c_password' => 'required|same:password',
        ]);

		$user = User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => bcrypt($request->password),
		]);
		$success['name'] = $user->name;
		$success['token'] = $user->createToken('MyApp', ['*'])->accessToken;
		return response()->json(['success' => $success], 200);
	}
}
