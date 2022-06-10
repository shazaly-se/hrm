<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//custom
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
//use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class ResetPasswordController extends Controller
{
    

    protected function sendResetResponse(Request $request)
    {
        //password.reset
        $input = $request->only('email','token', 'password', 'password_confirmation');
        $validator = Validator::make($input, [
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
        ]);
        if ($validator->fails()) {
        return response(['errors'=>$validator->errors()->all()], 422);
        }
        $oldtokenData = DB::table('password_resets')
        ->where('email', $request->email)->first();
        if( $oldtokenData)
        {
            $user = User::where('email',$request->email)->first();
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->update();
    
    
            $tokenData = DB::table('password_resets')
            ->where('email', $request->email)->delete();
    
             return response()->json([
            "success" => true,
            "message" => "Password reset successfully.",
            "data" => $user
            ]);
        }
        else
        {
            return response()->json([
                "success" => false,
                "message" => "Reset Error."
                ]);
        }



     
    }
}
