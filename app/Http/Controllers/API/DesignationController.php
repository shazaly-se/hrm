<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Designation;
use App\Models\Department;
class DesignationController extends Controller
{
    public function index(){
        $designations = Designation::join("departments","departments.id","=","designations.department_id")
        ->where("designations.is_deleted",0)->orderBy("created_at","desc")
        ->get(array("designations.*","departments.department"));
        return response()->json([
            "success" => true,
             'designations'=>$designations
            ]);
    }

    public function store(Request $request){
       
     $department= Department::find($request->department_id);
     if($department){
        $designation = Designation::create([
            'department_id' => $request->department_id,
            'designationName' => $request->designationName
        ]);
        $designations = Designation::join("departments","departments.id","=","designations.department_id")
        ->where("designations.is_deleted",0)->orderBy("created_at","desc")
        ->get(array("designations.*","departments.department"));
        if($designations){
        return response()->json([
            "success" => true,
            "message"=>"created successfully",
            'designations'=>$designations
           ]);
        }
     }else{
        return response()->json([
            "success" => false,
            "message"=>"No department",
            
           ]);
     }
     
    }

    public function designationById($id){
        $designation= Designation::join("departments","departments.id","=","designations.department_id")
        ->where("designations.id",$id)->first(array("designations.id","designations.designationName","departments.id as department_id","departments.department"));
        if(!$designation){
            return response()->json([
                "success" => false,
                "message"=>"designation ".$id ." does not exist",
               ]); 
        }
        return response()->json($designation);

    }

    public function update($id,Request $request){
        $designation= Designation::find($id);
        if(!$designation){
            return response()->json([
                "success" => false,
                "message"=>"designation ".$id ." does not exist",
               ]); 
        }

        $department= Department::find($request->department_id);
        if($department){
            $designation->department_id = $request->department_id;
            $designation->designationName = $request->designationName;
            if($designation->update()){

                $designations = Designation::join("departments","departments.id","=","designations.department_id")
                ->where("designations.is_deleted",0)->orderBy("created_at","desc")
                ->get(array("designations.*","departments.department"));

                return response()->json([
                     "success" => true,
                     "message"=>"updated successfully",
                     'designations'=>$designations
                    ]);
            }
        }else{
            return response()->json([
                "success" => false,
                "message"=>"No department",
                
               ]);
        }
    }

    public function destroy($id){
        $designation= Designation::find($id);
        if(!$designation){
            return response()->json([
                "success" => false,
                "message"=>"designation ".$id ." does not exist",
               ]); 
        }
        $designation->is_deleted = 1;
        if($designation->update()){
        $designations = Designation::join("departments","departments.id","=","designations.department_id")
        ->where("designations.is_deleted",0)->orderBy("created_at","desc")
        ->get(array("designations.*","departments.department"));
        return response()->json([
            "success" => true,
            "message"=>"deleted successfully",
            'designations'=>$designations
           ]);
        }
    }
}
