<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Role_Permission;
use DB;
class RolePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
           $allpermissions = DB::table("permissions")->get(array("permissions.id as id","permissions.name as name"));
           return response()->json(['allpermissions'=>$allpermissions]);
    }

    public function store(Request $request){
        $oldroles=Role::where("name",$request->role)->first();
        if($oldroles){
            return response()->json(['success'=>false]);
        }
        $role= new Role;
        $role->name=$request->role;
        if($role->save()){

        
        for($i=0;$i< count($request->permissions); $i++){
            $new_role_permission = new Role_Permission;
            $new_role_permission->role_id=$role->id;
            $new_role_permission->permission_id= $request->permissions[$i]["id"];
            $new_role_permission->save();
        // return $request->permissions[$i];
 
         }
         return response()->json(['success'=>true]);

        }

    }

    public function edit($id)
    {
       

           $role = Role::where("id",$id)->first();
           $permissions = DB::table("permissions")
           ->join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
           ->where("role_has_permissions.role_id",$id)
           ->get(array("permissions.id as id","permissions.name as name"));
           $allpermissions = DB::table("permissions")->get(array("permissions.id as id","permissions.name as name"));
        
           return response()->json(['role'=>$role,'permissions' => $permissions,'allpermissions'=>$allpermissions]);
        
    }



    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       //return $id;
       $oldpermissions = DB::table("role_has_permissions")->where("role_id",$id)->delete();
       //return $oldpermissions;

       for($i=0;$i< count($request->permissions); $i++){
           $new_role_permission = new Role_Permission;
           $new_role_permission->role_id=$id;
           $new_role_permission->permission_id= $request->permissions[$i]["id"];
           $new_role_permission->save();
       // return $request->permissions[$i];

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
