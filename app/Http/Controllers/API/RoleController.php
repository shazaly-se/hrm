<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Company;
use App\Models\PermissionRole;
use Spatie\Permission\Models\Permission;
use App\Models\BranchUsers;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use DB;
class RoleController extends Controller
{
    public function index()
    {
         $roles = Role::get();

        //  foreach($roles as $role){
        //     $role->permissions = DB::table("permissions")
        //     ->join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
        //     ->where("role_has_permissions.role_id",$role->id)
        //     ->get();
        //  }

        
         return response()->json($roles);
         
         
        // $id = Auth::id();
        // $company = Company::where('user_id',$id)->first();
        // if($company == null)
        // {
        //     $branch_id = BranchUsers::where('user_id',$id)->pluck('branch_id');
        //     $company_id = Branch::whereIn('id',$branch_id)->pluck('company_id');
        //     $roles = Role::with('permissions')->whereIn('company_id', $company_id)->get();
        // }
        // else
        // {
        //     $roles = Role::with('permissions')->where('company_id', $company->id)->get();
        // }       
        // $permissions = Permission::all();
        // return response()->json(['success' => true,'roles' => $roles, 'permissions' =>$permissions], 200);
    }

    public function show_permissions($id){
        return "permissions";
    }
    public function store(Request $request)
    {
        
        $role= new Role;
        $role->company_id= $request->company_id;
        $role->name=$request->name;
        $role->created_by = 1;
        $role->updated_by  = 1;
        $role->save();
        return response()->json([
        "success" => true,
        "message" => "Role created successfully.",
        "data" => $role
        ]);

        // $id = Auth::id();
        // $company = Company::where('user_id',$id)->first();
        // $input = $request->all();
        // $validator = Validator::make($input, [
        // // 'name' => 'required',
        // ]);
        // if($validator->fails()){
        // return $this->sendError('Validation Error.', $validator->errors());       
        // }
        // $input['company_id'] = $company->id;
        // $input['created_by'] = $id;
        // $input['updated_by'] = $id;
        // $role = Role::create($input);
        // $permissions = $request->permissions;
        // $integerIDs = array_map('intval', $permissions);
        // foreach ($integerIDs as $permission)
        // {
        //     $input['role_id'] = $role->id;
        //     $input['permission_id'] = $permission;
        //     $input['created_by'] = $id;
        //     $input['updated_by'] = $id;
        //     // $input['created_at'] = '';
        //     // $input['updated_at'] = '';
        //     $permission = PermissionRole::create($input);
        // }
        // return response()->json([
        // "success" => true,
        // "message" => "Role created successfully.",
        // "data" => $role
        // ]);
    }
    public function show($id)
    {
        $role = Role::with('permissions')->find($id);
        if (is_null($role)) {
        return $this->sendError('Role not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Role retrieved successfully.",
        "data" => $role
        ]);
    }
    public function updateById(Request $request, $id)
    {     
        $user_id = Auth::id();
        $company = Company::where('user_id',$user_id)->first();
        $input = $request->all();
        $validator = Validator::make($input, [
        // 'name' => 'required',
        // 'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }

        $role = Role::find($id);
        $role->name = $input['name'];
        $role->company_id = $company->id;
        $role->updated_by = $user_id;
        $role->save();
        $permissions = $request['permissions'];
        $permissions = $request->permissions;
        $permissions = array_map('intval', $permissions);
        $role->permissions()->syncWithPivotValues($permissions, ['created_by' => $user_id, 'updated_by' => $user_id]);
// dd('hi');


//



        // //delete role permissions
        // $rolePermissions = PermissionRole::where('role_id', $id)->get();
        // foreach ($rolePermissions as $rolePermission)
        // {
        //     // dd('hi');
        //     // dd($rolePermission->permission_id);
        //     // $rolePers = PermissionRole
        //     // $rolePers = PermissionRole::where('role_id', $id)->get();

        // //     $rolePers = PermissionRole::where([
        // //         'role_id' => $id,
        // //         'permission_id' => $rolePermission->permission_id,
        // //  ])->get();
        // // //  dd($rolePers);
        // //     if($rolePers)
        // //     {

        // //     }
        //     $rolePermission->delete();
        // }
        // //update role permissions
        // $permissions = $request->permissions;
        // $integerIDs = array_map('intval', $permissions);
        // foreach ($integerIDs as $permission)
        // {
        //     $input['role_id'] = $id;
        //     $input['permission_id'] = $permission;
        //     $input['created_by'] = $user_id;
        //     $input['updated_by'] = $user_id;
        //     // $input['created_at'] = '';
        //     // $input['updated_at'] = '';
        //     $permission = PermissionRole::create($input);
        // }
        $roles = Role::with('permissions')->where('id',$id)->get();
        return response()->json([
        "success" => true,
        "message" => "Role updated successfully.",
        "data" => $roles
        ]);
    }
    public function destroy($id)
    {
        //remove this code after migrate refresh, this is instead of delete cascade
        $rolePermissions = PermissionRole::where('role_id', $id)->get();
        foreach ($rolePermissions as $rolePermission)
        {
            $rolePermission->delete();
        }
        //
        $role = Role::find($id);
        $role->delete();
        return response()->json([
        "success" => true,
        "message" => "Role deleted successfully.",
        "data" => $role
        ]);
    }
}
