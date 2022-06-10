<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\AnnouncementDepartment;
use App\Models\AnnouncementType;
use App\Events\AnnouncementMessage;
use Carbon\Carbon;
use DB;
class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function announcement_type(){
        $announcementTypes = AnnouncementType::all();
        return response()->json(["announcementTypes"=>$announcementTypes]); 
    }
    public function index()
    {
  
        $announcements = Announcement::join("announcement_types","announcement_types.id","=","announcements.type_id")
                                     
                                     //->select("announcements.*",DB::raw("DAYNAME(announcements.created_at) as dayname"))
                                     //->take(3)
                                     ->get(array("announcements.*","announcement_types.name as type"));
                                     //->get(array("announcements.*"));
                                     return response()->json(["announcements"=>$announcements]); 
    }                                

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {  
        $announcement = new Announcement;
        $announcement->type_id = $request->type_id;
        $announcement->description = $request->description;
        if($announcement->save()){
        for($i=0;$i< count($request->departments); $i++){
            $announcementdepartment = new AnnouncementDepartment;
            $announcementdepartment->announcement_id= $announcement->id;
            $announcementdepartment->department_id= $request->departments[$i]["id"]; 
            $announcementdepartment->save();
            }
            event(new AnnouncementMessage($request->type_id,$request->departments,$request->description,Carbon::now()));
            return response()->json("success");
        }  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $announcements = Announcement::join("announcement_types","announcement_types.id","=","announcements.type_id")
                                ->where("announcements.id",$id)     
        //->select("announcements.*",DB::raw("DAYNAME(announcements.created_at) as dayname"))
        //->take(3)
        ->first(array("announcements.*","announcement_types.name as type_name"));
        $announcement_departments = DB::table("announcement_departments")
        ->join("departments","departments.id","=","announcement_departments.department_id")
        ->where("announcement_id",$id)->get(array("departments.id as id","departments.department as name"));
        //->get(array("announcements.*"));
        return response()->json(["announcements"=>$announcements,"departments"=>$announcement_departments]); 
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
        $announcement =  Announcement::where("id",$id)->first();
        $announcement->type_id = $request->type_id;
        $announcement->description = $request->description;
        if($announcement->update()){
             AnnouncementDepartment::where("announcement_id",$id)->delete();
        for($i=0;$i< count($request->departments); $i++){
            $announcementdepartment = new AnnouncementDepartment;
            $announcementdepartment->announcement_id= $announcement->id;
            $announcementdepartment->department_id= $request->departments[$i]["value"]; 
            $announcementdepartment->save();
            }
            event(new AnnouncementMessage($request->type_id,$request->departments,$request->description,Carbon::now()));
            return response()->json("success");
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
        $announcement =  Announcement::where("id",$id)->first();
        $announcement->delete();

        AnnouncementDepartment::where("announcement_id",$id)->delete();
        return response()->json("success");

    }
}
