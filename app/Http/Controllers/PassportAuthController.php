<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
class PassportAuthController extends Controller
{
    /**
     * Registration
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
 
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
       
        $token = $user->createToken('LaravelAuthApp')->accessToken;
 
        return response()->json(['token' => $token], 200);
    }
 
    /**
     * Login
     */
    public function login(Request $request)
    {
       // return $request->all();
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;

            $user = auth()->user()->join('employeeinfos','employeeinfos.user_id',"users.id")->first(array("users.*","employeeinfos.image"));
			$token= auth()->user()->createToken('LaravelAuthApp')->accessToken;
			return response()->json(['success' =>true,"token"=> $token,"user"=>$user], 200);

           
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }   
}