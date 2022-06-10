<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class DepartmentController extends Controller
{

    //  function __construct()
    // {
    //     $this->middleware('permission:list department', ['only' => ['index','store']]);

    // }
    
    public function index(){
        $departments = Department::where("is_deleted",0)->orderBy("created_at","desc")->get();
        return response()->json([
            "success" => true,
             'departments'=>$departments
            ]);
    }

    public function alldepartments(){
       $departments = Department::orderBy("created_at","desc")->get(array("departments.id as id","departments.department as name"));
        return response()->json([
            "success" => true,
             'departments'=>$departments
            ]);
    }

    

    public function store(Request $request){
        //return $request->all();
        $department = new Department;
        $department->department = $request->department;
        if($department->save()){
            $departments=Department::where("is_deleted",0)->orderBy("created_at","desc")->get();
            return response()->json([
                 "success" => true,
                 "message"=>"created successfully",
                 'departments'=>$departments
                ]);
        }

    }
    public function departmentById($id){
        $department= Department::find($id);
        if(!$department){
            return response()->json([
                "success" => false,
                "message"=>"department ".$id ." does not exist",
               ]); 
        }
        return response()->json($department);

    }

    public function update($id,Request $request){

   
        $department= Department::find($id);
        if(!$department){
            return response()->json([
                "success" => false,
                "message"=>"department ".$id ." does not exist",
               ]); 
        }

        $department->department = $request->department;
        if($department->update()){
            $departments=Department::where("is_deleted",0)->orderBy("created_at","desc")->get();
            return response()->json([
                 "success" => true,
                 "message"=>"updated successfully",
                 'departments'=>$departments
                ]);
        }
    }

    public function destroy($id){
        $department= Department::find($id);
        if(!$department){
            return response()->json([
                "success" => false,
                "message"=>"department ".$id ." does not exist",
               ]); 
        }
        $department->is_deleted=1;
        if($department->update()){
        $departments=Department::where("is_deleted",0)->orderBy("created_at","desc")->get();
        return response()->json([
            "success" => true,
            "message"=>"deleted successfully",
            'departments'=>$departments
           ]);
        }
    }
}
