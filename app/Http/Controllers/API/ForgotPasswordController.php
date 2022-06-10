<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//custom
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    protected function sendResetLinkResponse(Request $request)
    {
        $tokenData = '';
        $input = $request->only('email');
        $validator = Validator::make($input, [
        'email' => "required|email"
        ]);
        if ($validator->fails()) {
        return response(['errors'=>$validator->errors()->all()], 422);
        }
        if(User::where('email', $request->email)->first())
        {
            //Get the token just created above
            $tokenData = DB::table('password_resets')
            ->where('email', $request->email)->first();
            if($tokenData == null)
            {
               //Create Password Reset Token
             DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => Str::random(40),
                'created_at' => Carbon::now()
            ]);
            $tokenData = DB::table('password_resets')
            ->where('email', $request->email)->first();
            }
            $token = $tokenData->token;
            // dd($request->email);
            $details = [
                'title' => 'Reset Password Link from Find Properties',
                'body' => 'This is for testing email using smtp',
                'token' => $token,
                'email' => $request->email
            ];
            \Mail::to($request->email)->send(new \App\Mail\PasswordResetLinkMail($details));
                $message = "Mail send successfully";        
        }
        else
        {
            $message = "This email is not register into the system";
        }
        $response = ['data'=>$tokenData,'message' => $message];
        return response($response, 200);
        
     
    }

    
}
