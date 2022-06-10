<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use DB;
class UserManagementController extends Controller
{
    public function index(){
        $users = User::join("model_has_roles","model_has_roles.model_id","users.id")
                     ->join("roles","roles.id","model_has_roles.role_id")
        ->get(array("users.*","roles.name as role"));
                // dd($leave);
                return response()->json([
                    "success" => true,
                    "message" => "user List",
                    "users" => $users,
      
                    ]);

    }

    public function edit($id){
        $user = User::join("model_has_roles","model_has_roles.model_id","users.id")
                     ->join("roles","roles.id","model_has_roles.role_id")
                     ->where("users.id",$id)
        ->first(array("users.*","roles.id as role_id","roles.name as role"));
                // dd($leave);
                return response()->json(["user"=>$user]);

    }

    public function update(Request $request,$id){
   
        $user = User::where("users.id",$id)
       ->first();
       
        //return $user_role;
        $user->name=$request->name;
        $user->email = $request->email;
        $user->activation = $request->activation;
            $user->update();
            $user_role = DB::table("model_has_roles")->where("model_id",$id)->update(['role_id'=>$request->role_id]);
            // $user_role->role_id= $request->role_id;
            // $user_role->save(); 
            $updateduser = User::join("model_has_roles","model_has_roles.model_id","users.id")
                               ->join("roles","roles.id","model_has_roles.role_id")
                               ->where("users.id",$id)
                               ->first(array("users.*","roles.id as role_id","roles.name as role"));
                               return response()->json(["user"=>$updateduser]);
        

    }
}
