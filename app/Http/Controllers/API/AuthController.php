<?php

namespace App\Http\Controllers\API;
// namespace App\Models;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
		
        return $request->all();
        $request->validate([
            'email' => 'required|email',
    		'password' => 'required',
        ]);
    	
        $credentials = $request->post();
		//return $credentials;
        
    	if (Auth::attempt($credentials)) {
			
			$user = Auth::user();
			$token= $user->createToken('MyApp')->accessToken;
			return response()->json(['success' =>true,"token"=> $token,"user"=>$user], 200);
		}
		else {
			return response()->json(["success" => false,"hasError" =>true,
			'msg' => 'Unauthorized user']);
		}
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
