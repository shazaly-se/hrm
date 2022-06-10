<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Leavetype;
use App\Models\Leavestatus;

use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index(){
        $leavetypes = Leavetype::all();

        return response()->json([
            "success" => true,
            "message" => "Leave types",
            "leavetypes"=>$leavetypes,
            ]);
    }

    public function leavestatus(){
        $leavestatus = Leavestatus::all();

        return response()->json([
            "success" => true,
            "message" => "Leave status",
            "leavestatus"=>$leavestatus,
            ]);
    }
}
