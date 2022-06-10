<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ChangePasswordController extends Controller {

    public function passwordResetProcess(Request $request){
      


          
      if(strcmp($request->password, $request->password_confirmation) != 0){
        //Current password and new password are same
        return response()->json(["success" => false,"hasError" =>true, 'msg'=>"Password and confirm  password mismatch."]);
       } 
       
      return $this->updatePasswordRow($request)->count() > 0 ? $this->resetPassword($request) : $this->tokenNotFoundError();
    }

    // Verify if token is valid
    private function updatePasswordRow($request){
     // return $request->all();
       return DB::table('recover_password')->where([
           'email' => $request->email,
           'token' => $request->token
       ]);
    }

    // Token not found response
    private function tokenNotFoundError() {
        return response()->json([
            "success" => false,"hasError" =>true,
          'msg' => 'Either your email or token is wrong.'
        ]);
    }

    // Reset password
    private function resetPassword($request) {
        // find email
      //  return "trying change";
        $userData = User::whereEmail($request->email)->first();
     
        // update password
        $userData->update([
          'password'=>bcrypt($request->password)
        ]);
        // remove verification data from db
        $this->updatePasswordRow($request)->delete();

        // reset password response
        return response()->json(["success" => true,"hasError" =>false, 'msg'=>"Your password updated"],Response::HTTP_CREATED);
    }    

}