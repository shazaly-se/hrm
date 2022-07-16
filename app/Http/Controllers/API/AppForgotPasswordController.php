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
use Illuminate\Support\Facades\Http;
class AppForgotPasswordController extends Controller
{
    protected function sendResetMsgResponse(Request $request)
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
                'code'=>random_int(100000, 999999),
                'created_at' => Carbon::now()
            ]);
            $tokenData = DB::table('password_resets')
            ->where('email', $request->email)->first();
            }
            $token = $tokenData->token;
            // dd($request->email);
            $details = [
                'msg' => 'use this code',
                'code' => $tokenData->code
            ];
            $response= Http::post("https://elitbuzz-me.com/sms/smsapi?api_key=C200343061a1e16b4924a3.21883164&type=text&contacts=00971547963078&senderid=MFRE&msg=Your OTP is  ". $tokenData->code);
            return $response;
            //\Mail::to($request->email)->send(new \App\Mail\PasswordResetLinkMail($details));
               // $message = "Mail send successfully";        
        }
        else
        {
            $message = "This email is not register into the system";
        }
        $response = ['data'=>$tokenData,'message' => $message];
        return response($response, 200);
        
     
    }
}
