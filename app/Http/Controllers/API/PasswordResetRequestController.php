<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class PasswordResetRequestController extends Controller {
  
    public function sendPasswordResetEmail(Request $request){
  
      
        // If email does not exist
        if(!$this->validEmail($request->email)) {
            return response()->json([
                "success" => false,"hasError" =>true,
                'msg' => 'Email does not exist.'
            ]);
        } else {
            // If email exists
            $this->sendMail($request->email);
            return response()->json([
                "success" => true,"hasError" =>false,
                'msg' => 'Check your inbox, we have sent a link to reset email.'
            ]);            
        }
    }


    public function sendMail($email){
     
        $token = $this->generateToken($email);
        //return $token;
        //return "sjhsj";
        Mail::to($email)->send(new SendMail($token,$email));
  
       return response()->json(["msg"=>"suucess"]);
    }

    public function validEmail($email) {
       return !!User::where('email', $email)->first();
    }

    public function generateToken($email){
      $isOtherToken = DB::table('recover_password')->where('email', $email)->first();

      if($isOtherToken) {
        return $isOtherToken->token;
      }

      $token = Str::random(80);
      $this->storeToken($token, $email);
      return $token;
    }

    public function storeToken($token, $email){
        DB::table('recover_password')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()            
        ]);
    }
    
}